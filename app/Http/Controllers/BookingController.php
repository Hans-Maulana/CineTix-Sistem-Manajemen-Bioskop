<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Schedule;
use App\Models\Seat;
use App\Models\TicketBooking;
use App\Models\Payment;
use App\Models\Promo;
use App\Services\Payment\PaymentContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * Tampilkan form booking untuk schedule tertentu
     */
    public function show(Schedule $schedule)
    {
        $schedule->load('film', 'studio.seats');

        // Get already booked seats untuk schedule ini
        $bookedSeatIds = TicketBooking::whereHas('booking', function ($q) {
            $q->whereIn('status', ['pending', 'confirmed']);
        })
            ->where('schedule_id', $schedule->id)
            ->pluck('seat_id')
            ->toArray();

        // Map seats by code untuk lookup cepat di view
        $seatsByCode = $schedule->studio->seats->keyBy('seat_code');

        return view('booking.show', compact('schedule', 'bookedSeatIds', 'seatsByCode'));
    }

    /**
     * Store booking dengan real-time seat locking
     * Menggunakan transaction untuk mencegah race condition
     */
   public function store(Request $request)
    {
        if (is_string($request->seat_ids)) {
            $request->merge(['seat_ids' => explode(',', $request->seat_ids)]);
        }

        $validated = $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'seat_ids' => 'required|array|min:1',
            'seat_ids.*' => 'exists:seats,id',
            'promo_code' => 'nullable|string',
        ]);

        try {
            $booking = DB::transaction(function () use ($validated) {
                $schedule = Schedule::with('film')->find($validated['schedule_id']);
                $seatIds = $validated['seat_ids'];

                // 1. PESSIMISTIC LOCKING: Kunci kursi saat dicek
                $seats = Seat::whereIn('id', $seatIds)
                    ->where('studio_id', $schedule->studio_id)
                    ->lockForUpdate()
                    ->get();

                // 2. STATE PATTERN: Cek apakah kursi masih available atau waktu pendingnya sudah habis
                foreach ($seats as $seat) {
                    if (!$seat->isAvailable()) { // Menggunakan method dari Model Seat yang kita buat
                        throw new \Exception("Maaf, kursi {$seat->seat_code} baru saja diambil orang lain.");
                    }
                }

                // Kalkulasi harga & promo
                $totalAmount = $schedule->ticket_price * count($seatIds);
                $promoId = null;

                if (!empty($validated['promo_code'])) {
                    $promo = Promo::where('code', $validated['promo_code'])
                        ->where('valid_until', '>=', now())
                        ->first();
                    if ($promo) {
                        $totalAmount -= $promo->disc_amount;
                        $promoId = $promo->id;
                    }
                }

                // Buat Booking
                $booking = Booking::create([
                    'user_id' => Auth::id(),
                    'promo_id' => $promoId,
                    'booking_type' => 'ticket',
                    'total_amount' => max(0, $totalAmount),
                    'status' => 'pending',
                    'qr_redeem' => \Illuminate\Support\Str::random(15),
                ]);

                // 3. UBAH STATE & SET TIMER
                foreach ($seats as $seat) {
                    TicketBooking::create([
                        'booking_id' => $booking->id,
                        'schedule_id' => $schedule->id,
                        'seat_id' => $seat->id,
                        'price_at_sale' => $schedule->ticket_price,
                    ]);

                    $seat->update([
                        'status' => 'pending',
                        'locked_until' => now()->addMinutes(5), // Timer anti-nyangkut
                        'locked_by_user_id' => Auth::id()
                    ]);

                    // 4. OBSERVER PATTERN: Broadcast agar layar user lain jadi abu-abu
                    broadcast(new \App\Events\SeatStatusUpdated($seat->id, 'pending'))->toOthers();
                }

                return $booking;
            }, 3);

            return redirect()->route('booking.payment', $booking)
                ->with('success', 'Kursi berhasil di-lock sementara. Segera lakukan pembayaran dalam 5 menit.');

        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Tampilkan pilihan metode pembayaran (Strategy Pattern)
     */
    public function payment(Booking $booking)
    {
        // Pastikan hanya pemilik booking yang bisa akses
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        if ($booking->status === 'confirmed') {
            return redirect()->route('booking.confirmation', $booking);
        }

        $booking->load(['ticketBookings.seat', 'ticketBookings.schedule.film', 'promo']);
        $paymentMethods = PaymentContext::availableMethods();

        return view('booking.payment', compact('booking', 'paymentMethods'));
    }

    /**
     * Initiate payment dengan metode yang dipilih (Strategy Pattern)
     * Setiap kali dipanggil, buat Payment baru dengan status pending
     */
    public function initiatePayment(Request $request, Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'payment_method' => 'required|in:qris,virtual_account',
        ]);

        try {
            // Resolve strategy berdasarkan metode
            $strategy = PaymentContext::resolve($validated['payment_method']);

            // Buat payment baru (selalu buat baru dengan status pending)
            $payment = $strategy->initiate($booking);

            return redirect()->route('booking.process-payment', [
                'booking' => $booking,
                'payment' => $payment,
            ]);

        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal memulai pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan halaman proses pembayaran (QR code / VA number + countdown)
     */
    public function processPayment(Booking $booking, Payment $payment)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        if ($payment->booking_id !== $booking->id) {
            abort(404);
        }

        // Jika payment sudah expired, mark as failed
        if ($payment->status === 'pending' && $payment->isExpired()) {
            $payment->markAsFailed();
            return redirect()->route('booking.payment', $booking)
                ->with('error', 'Waktu pembayaran telah habis. Silakan coba lagi.');
        }

        // Jika sudah success, redirect ke confirmation
        if ($payment->status === 'success') {
            return redirect()->route('booking.confirmation', $booking);
        }

        $booking->load(['ticketBookings.seat', 'ticketBookings.schedule.film', 'promo']);

        // Get display data dari strategy
        $strategy = PaymentContext::resolve($payment->method);
        $displayData = $strategy->getDisplayData($payment, $booking);

        return view('booking.process-payment', compact('booking', 'payment', 'displayData'));
    }

    /**
     * Selesaikan pembayaran (simulasi - tombol "Selesaikan Pembayaran")
     */
    public function confirmPayment(Request $request, Booking $booking, Payment $payment)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        if ($payment->booking_id !== $booking->id) {
            abort(404);
        }

        // Cek apakah payment masih pending
        if ($payment->status !== 'pending') {
            return redirect()->route('booking.payment', $booking)
                ->with('error', 'Payment ini sudah diproses sebelumnya.');
        }

        // Cek expired
        if ($payment->isExpired()) {
            $payment->markAsFailed();
            return redirect()->route('booking.payment', $booking)
                ->with('error', 'Waktu pembayaran telah habis. Silakan coba lagi.');
        }

      try {
            DB::transaction(function () use ($payment, $booking) {
                // Proses payment via Strategy Pattern Hasan
                $strategy = PaymentContext::resolve($payment->method);
                $strategy->process($payment);

                // FINALISASI STATE: Ubah status kursi dari pending menjadi booked
                $ticketBookings = $booking->ticketBookings()->with('seat')->get();
                foreach ($ticketBookings as $ticket) {
                    $seat = $ticket->seat;
                    $seat->update([
                        'status' => 'booked',
                        'locked_until' => null, // Matikan timer
                    ]);

                    // Broadcast lagi kalau kursi ini sudah fix laku
                    broadcast(new \App\Events\SeatStatusUpdated($seat->id, 'booked'))->toOthers();
                }
            });

            return redirect()->route('booking.confirmation', $booking)
                ->with('success', 'Pembayaran berhasil! Tiket Anda telah dikonfirmasi.');

        } catch (\Throwable $e) {
            $payment->markAsFailed();
            return redirect()->route('booking.payment', $booking)
                ->with('error', 'Pembayaran gagal: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan confirmation ticket
     */
    public function confirmation(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        $booking->load(['ticketBookings.seat', 'ticketBookings.schedule.film', 'ticketBookings.schedule.studio', 'user', 'payments']);

        return view('booking.confirmation', compact('booking'));
    }

    /**
     * Tampilkan daftar tiket aktif (confirmed)
     */
    public function tickets()
    {
        $bookings = Auth::user()->bookings()
            ->where('status', 'confirmed')
            ->with('ticketBookings.schedule.film', 'ticketBookings.schedule.studio', 'payments')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('booking.tickets', compact('bookings'));
    }

    /**
     * Tampilkan histori semua transaksi
     */
    public function history()
    {
        $bookings = Auth::user()->bookings()
            ->with('ticketBookings.schedule.film', 'payments', 'latestPayment')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('booking.history', compact('bookings'));
    }

    /**
     * Batalkan booking (jika belum dikonfirmasi)
     */
    public function cancel(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        if ($booking->status !== 'pending') {
            return back()->with('error', 'Hanya booking dengan status pending yang bisa dibatalkan.');
        }

        try {
            DB::transaction(function () use ($booking) {
                // Free up seats
                $ticketBookings = $booking->ticketBookings()->with('seat')->get();

                foreach ($ticketBookings as $ticket) {
                    $seat = $ticket->seat;
                    $seat->update(['status' => 'available']);
                }

                // Mark pending payments as failed
                $booking->payments()->where('status', 'pending')->update(['status' => 'failed']);

                // Update booking status
                $booking->update(['status' => 'cancelled']);
            });

            return redirect()->route('booking.history')
                ->with('success', 'Booking berhasil dibatalkan.');

        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal membatalkan booking: ' . $e->getMessage());
        }
    }

    /**
     * Get available seats untuk schedule (AJAX)
     */
    public function getAvailableSeats(Schedule $schedule)
    {
        $bookedSeats = TicketBooking::whereHas('booking', function ($q) {
            $q->where('status', '!=', 'cancelled');
        })
            ->where('schedule_id', $schedule->id)
            ->pluck('seat_id')
            ->toArray();

        $availableSeats = $schedule->studio->seats()
            ->whereNotIn('id', $bookedSeats)
            ->get(['id', 'row_label', 'seat_number', 'seat_code', 'status']);

        return response()->json([
            'seats' => $availableSeats,
            'ticketPrice' => $schedule->ticket_price,
        ]);
    }

    /**
     * Get booking details (AJAX)
     */
    public function getBookingDetails(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        return response()->json(
            $booking->load('ticketBookings.seat', 'ticketBookings.schedule', 'payments', 'promo')
        );
    }

    /**
     * Check payment status (AJAX - untuk auto-check dari frontend)
     */
    public function checkPaymentStatus(Payment $payment)
    {
        if ($payment->booking->user_id !== Auth::id()) {
            abort(403);
        }

        return response()->json([
            'status' => $payment->status,
            'remaining_seconds' => $payment->remaining_seconds,
            'is_expired' => $payment->isExpired(),
        ]);
    }
}
