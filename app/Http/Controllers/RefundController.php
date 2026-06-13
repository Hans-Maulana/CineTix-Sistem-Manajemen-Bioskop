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
                'refund_status'       => 'requested',
                'refund_reason'       => $validated['refund_reason'],
                'refund_amount'       => $booking->refundNetAmount(),
                'refund_requested_at' => now(),
            ]);

            return redirect()->route('booking.history')
                ->with('success', 'Pengajuan refund berhasil dikirim. Admin akan mereview dalam 1×24 jam.');
        } catch (\Throwable $e) {
            Log::error('Refund store error: ' . $e->getMessage(), ['booking_id' => $booking->id]);
            return back()->with('error', 'Gagal mengajukan refund. Silakan coba lagi.');
        }
    }

    // ─────────────────────────────────────────────
    //  ADMIN AREA
    // ─────────────────────────────────────────────

    /**
     * Daftar semua pengajuan refund untuk admin.
     */
    public function adminIndex(Request $request)
    {
        $query = Booking::with([
            'user',
            'ticketBookings.schedule.film',
            'ticketBookings.seat',
            'latestPayment',
        ])->whereNotNull('refund_status');

        // Filter by status
        $filterStatus = $request->input('status', 'requested');
        if (in_array($filterStatus, ['requested', 'approved', 'rejected'])) {
            $query->where('refund_status', $filterStatus);
        }

        $refunds = $query->orderByDesc('refund_requested_at')->paginate(15)->withQueryString();

        $counts = [
            'requested' => Booking::where('refund_status', 'requested')->count(),
            'approved'  => Booking::where('refund_status', 'approved')->count(),
            'rejected'  => Booking::where('refund_status', 'rejected')->count(),
        ];

        return view('admin.refunds.index', compact('refunds', 'filterStatus', 'counts'));
    }

    /**
     * Admin menyetujui refund.
     */
    public function approve(Booking $booking)
    {
        if ($booking->refund_status !== 'requested') {
            return back()->with('error', 'Refund ini sudah diproses sebelumnya.');
        }

        try {
            DB::transaction(function () use ($booking) {
                // Update booking status
                $booking->update([
                    'status'               => 'refunded',
                    'refund_status'        => 'approved',
                    'refund_processed_at'  => now(),
                    'refund_processed_by'  => Auth::id(),
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
                            Log::error('Pusher Error (Refund Approve): ' . $e->getMessage());
                        }
                    }
                }
            });

            // Kirim email notifikasi ke customer
            $this->sendRefundApprovedMail($booking->fresh()->load(['user', 'ticketBookings.schedule.film']));

            return back()->with('success', "Refund #$booking->id berhasil disetujui. Kursi sudah dikembalikan.");
        } catch (\Throwable $e) {
            Log::error('Refund approve error: ' . $e->getMessage(), ['booking_id' => $booking->id]);
            return back()->with('error', 'Gagal memproses refund: ' . $e->getMessage());
        }
    }

    /**
     * Admin menolak refund.
     */
    public function reject(Request $request, Booking $booking)
    {
        if ($booking->refund_status !== 'requested') {
            return back()->with('error', 'Refund ini sudah diproses sebelumnya.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|min:5|max:500',
        ], [
            'rejection_reason.required' => 'Alasan penolakan wajib diisi.',
            'rejection_reason.min'      => 'Alasan penolakan minimal 5 karakter.',
        ]);

        try {
            $booking->update([
                'refund_status'           => 'rejected',
                'refund_rejection_reason' => $validated['rejection_reason'],
                'refund_processed_at'     => now(),
                'refund_processed_by'     => Auth::id(),
            ]);

            // Kirim email notifikasi ke customer
            $this->sendRefundRejectedMail(
                $booking->fresh()->load(['user', 'ticketBookings.schedule.film']),
                $validated['rejection_reason']
            );

            return back()->with('success', "Refund #$booking->id berhasil ditolak.");
        } catch (\Throwable $e) {
            Log::error('Refund reject error: ' . $e->getMessage(), ['booking_id' => $booking->id]);
            return back()->with('error', 'Gagal menolak refund: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────
    //  PRIVATE HELPERS
    // ─────────────────────────────────────────────

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

    private function sendRefundRejectedMail(Booking $booking, string $reason): void
    {
        $email = $booking->customerEmail();
        if (!$email) {
            return;
        }
        try {
            Mail::to($email)->send(new RefundRejectedMail($booking, $reason));
        } catch (\Throwable $e) {
            Log::error('Gagal kirim email refund rejected: ' . $e->getMessage(), ['booking_id' => $booking->id]);
        }
    }
}
