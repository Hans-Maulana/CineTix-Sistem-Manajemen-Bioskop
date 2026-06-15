<?php

namespace App\Http\Controllers;

use App\Mail\RefundApprovedMail;
use App\Mail\RefundRejectedMail;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RefundController extends Controller
{
    /**
     * Tampilkan form pengajuan refund untuk customer (login required).
     * Jika guest mencoba akses, redirect ke login.
     */
    public function request(Request $request, Booking $booking)
    {
        // Jika belum login → redirect ke login dengan URL tujuan setelah login
        if (!Auth::check()) {
            return redirect()->route('login', ['redirect' => url()->current()])
                ->with('info', 'Silakan login terlebih dahulu untuk mengajukan refund.');
        }

        // Hanya pemilik booking yang bisa akses
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $booking->load(['ticketBookings.seat', 'ticketBookings.schedule.film', 'ticketBookings.schedule.studio']);

        if (!$booking->canRequestRefund()) {
            return redirect()->route('booking.history')
                ->with('error', $this->refundNotEligibleMessage($booking));
        }

        return view('booking.refund-request', compact('booking'));
    }

    /**
     * Proses submit pengajuan refund dari customer.
     */
    public function store(Request $request, Booking $booking)
    {
        if (!Auth::check()) {
            return redirect()->route('login', ['redirect' => route('booking.refund.request', $booking)]);
        }

        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        $booking->load(['ticketBookings.seat', 'ticketBookings.schedule.film']);

        if (!$booking->canRequestRefund()) {
            return redirect()->route('booking.history')
                ->with('error', $this->refundNotEligibleMessage($booking));
        }

        $validated = $request->validate([
            'refund_reason' => 'required|string|min:10|max:1000',
        ], [
            'refund_reason.required' => 'Alasan refund wajib diisi.',
            'refund_reason.min'      => 'Alasan refund minimal 10 karakter.',
            'refund_reason.max'      => 'Alasan refund maksimal 1000 karakter.',
        ]);

        try {
            $booking->update([
                'refund_amount'       => $booking->refundNetAmount(),
            ]);

            $this->processAutoRefund($booking);

            return redirect()->route('booking.history')
                ->with('success', 'Refund berhasil disetujui secara otomatis. Dana akan segera dikembalikan ke rekening Anda.');
        } catch (\Throwable $e) {
            Log::error('Refund store error: ' . $e->getMessage(), ['booking_id' => $booking->id]);
            return back()->with('error', 'Gagal mengajukan refund. Silakan coba lagi.');
        }
    }

    /**
     * Tampilkan form refund khusus untuk guest (via token).
     */
    public function guestRequest(Request $request, Booking $booking)
    {
        $token = $request->query('token');

        if (!$booking->access_token || !hash_equals($booking->access_token, $token)) {
            abort(403, 'Akses tidak sah atau link tidak valid.');
        }

        if (!$booking->canGuestRequestRefund()) {
            return redirect()->route('booking.guest-ticket', ['booking' => $booking, 'token' => $token])
                ->with('error', 'Maaf, refund tidak bisa diajukan saat ini (sudah lewat batas waktu atau status tidak valid).');
        }

        return view('booking.guest-refund-request', compact('booking', 'token'));
    }

    /**
     * Proses pengajuan refund khusus guest (via token).
     */
    public function guestStore(Request $request, Booking $booking)
    {
        $token = $request->query('token');

        if (!$booking->access_token || !hash_equals($booking->access_token, $token)) {
            abort(403, 'Akses tidak sah atau link tidak valid.');
        }

        if (!$booking->canGuestRequestRefund()) {
            return redirect()->route('booking.guest-ticket', ['booking' => $booking, 'token' => $token])
                ->with('error', 'Maaf, refund tidak bisa diajukan saat ini.');
        }

        $validated = $request->validate([
            'guest_email'    => 'required|email',
            'refund_reason'  => 'required|string|min:10|max:1000',
            'bank_name'      => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
            'account_name'   => 'required|string|max:100',
        ], [
            'guest_email.required'    => 'Alamat email wajib diisi untuk verifikasi.',
            'guest_email.email'       => 'Format email tidak valid.',
            'refund_reason.required'  => 'Alasan refund wajib diisi.',
            'refund_reason.min'       => 'Alasan refund minimal 10 karakter.',
            'refund_reason.max'       => 'Alasan refund maksimal 1000 karakter.',
            'bank_name.required'      => 'Nama bank / E-Wallet wajib diisi.',
            'account_number.required' => 'Nomor rekening wajib diisi.',
            'account_name.required'   => 'Nama pemilik rekening wajib diisi.',
        ]);

        if (strtolower(trim($validated['guest_email'])) !== strtolower(trim($booking->customerEmail()))) {
            return back()->with('error', 'Alamat email tidak cocok dengan data pemesanan. Verifikasi gagal.')->withInput();
        }

        try {
            $booking->update([
                'refund_amount'       => $booking->refundNetAmount(),
            ]);

            $this->processAutoRefund($booking);

            return redirect()->route('booking.guest-ticket', ['booking' => $booking, 'token' => $token])
                ->with('success', 'Refund berhasil disetujui secara otomatis. Dana akan segera dikembalikan ke rekening Anda.');
        } catch (\Throwable $e) {
            Log::error('Guest Refund store error: ' . $e->getMessage(), ['booking_id' => $booking->id]);
            return back()->with('error', 'Gagal mengajukan refund. Silakan coba lagi.')->withInput();
        }
    }


    // ─────────────────────────────────────────────
    //  PRIVATE HELPERS
    // ─────────────────────────────────────────────

    private function processAutoRefund(Booking $booking): void
    {
        DB::transaction(function () use ($booking) {
            $booking->update([
                'status'               => 'refunded',
            ]);

            // Bebaskan kursi kembali menjadi available
            foreach ($booking->ticketBookings()->with('seat')->get() as $ticket) {
                if ($ticket->seat) {
                    $ticket->seat->update([
                        'status'      => 'available',
                        'locked_until' => null,
                    ]);

                    try {
                        broadcast(new \App\Events\SeatStatusUpdated($ticket->seat->id, 'available'))->toOthers();
                    } catch (\Exception $e) {
                        Log::error('Pusher Error (Auto Refund): ' . $e->getMessage());
                    }
                }
            }
        });

        // Kirim email notifikasi ke customer
        $this->sendRefundApprovedMail($booking->fresh()->load(['user', 'ticketBookings.schedule.film']));
    }

    private function refundNotEligibleMessage(Booking $booking): string
    {
        if ($booking->user_id === null) {
            return 'Maaf, pemesanan sebagai tamu tidak dapat direfund. Silakan login untuk mengajukan refund.';
        }
        if ($booking->status !== 'confirmed') {
            return 'Refund hanya bisa diajukan untuk booking yang sudah dikonfirmasi.';
        }
        if (!is_null($booking->refund_status)) {
            return 'Anda sudah pernah mengajukan refund untuk booking ini.';
        }
        return 'Maaf, refund tidak bisa diajukan. Pastikan film belum tayang minimal ' . \App\Models\Booking::REFUND_MIN_HOURS_BEFORE . ' jam ke depan.';
    }

    private function sendRefundApprovedMail(Booking $booking): void
    {
        $email = $booking->customerEmail();
        if (!$email) {
            return;
        }
        try {
            Mail::to($email)->send(new RefundApprovedMail($booking));
        } catch (\Throwable $e) {
            Log::error('Gagal kirim email refund approved: ' . $e->getMessage(), ['booking_id' => $booking->id]);
        }
    }


}
