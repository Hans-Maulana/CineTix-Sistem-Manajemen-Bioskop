<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Schedule;
use App\Models\Seat;
use App\Models\TicketBooking;
use App\Models\Payment;
use App\Models\Promo;
use App\Services\Payment\PaymentContext;
<<<<<<< Updated upstream
=======
use App\Services\ChainOfResponsibility\PaymentValidationChain;
use App\Services\ChainOfResponsibility\BookingApprovalChain;
use App\Services\ChainOfResponsibility\CancellationChain;
use App\Mail\TicketConfirmationMail;
use App\Support\GuestBookingAccess;
>>>>>>> Stashed changes
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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

        // Check if user is authenticated
        $isAuthenticated = Auth::check();
        $user = Auth::user();

        return view('booking.show', compact('schedule', 'bookedSeatIds', 'seatsByCode', 'isAuthenticated', 'user'));
    }

    /**
     * Store booking dengan real-time seat locking
     * Menggunakan transaction untuk mencegah race condition
     */
<<<<<<< Updated upstream
    public function store(Request $request)
        {
            if (is_string($request->seat_ids)) {
                $request->merge(['seat_ids' => explode(',', $request->seat_ids)]);
=======
   public function store(Request $request)
    {
        if (is_string($request->seat_ids)) {
            $request->merge(['seat_ids' => explode(',', $request->seat_ids)]);
        }

        $rules = [
            'schedule_id' => 'required|exists:schedules,id',
            'seat_ids' => 'required|array|min:1',
            'seat_ids.*' => 'exists:seats,id',
            'promo_code' => 'nullable|string',
        ];

        if (!Auth::check()) {
            $rules['guest_email'] = 'required|email:rfc,filter|max:255';
        }

        $validated = $request->validate($rules);

        try {
            // RUN BOOKING APPROVAL CHAIN
            $chain = BookingApprovalChain::build();
            $approval = $chain->handle($validated);

            if (!$approval['approved']) {
                return back()->with('error', $approval['message']);
>>>>>>> Stashed changes
            }

        $rules = [
        'schedule_id' => 'required|exists:schedules,id',
        'seat_ids' => 'required|array|min:1',
        'seat_ids.*' => 'exists:seats,id',
        'promo_code' => 'nullable|string',
    ];

    // guest -> belum login
    if (!Auth::check()) {
        $rules['guest_name'] = 'required|string|max:255';
        $rules['guest_email'] = 'required|email|max:255';
    }

    $validated = $request->validate($rules);

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

                $isGuest = empty($bookingData['user_id']);

                // Buat Booking
<<<<<<< Updated upstream
               $booking = Booking::create([
                    // Jika login pakai user_id, jika tidak (guest) maka null
                    'user_id' => Auth::check() ? Auth::id() : null,
                    // guest simpan nama & email
                    'guest_name' => Auth::check() ? null : $validated['guest_name'],
                    'guest_email' => Auth::check() ? null : $validated['guest_email'],
=======
                $booking = Booking::create([
                    'user_id' => $bookingData['user_id'] ?? null,
                    'guest_email' => $isGuest ? ($bookingData['guest_email'] ?? null) : null,
                    'access_token' => $isGuest ? Str::random(64) : null,
>>>>>>> Stashed changes
                    'promo_id' => $promoId,
                    'schedule_id' => $schedule->id,
                    'booking_type' => 'ticket',
                    'total_amount' => max(0, $totalAmount),
                    'status' => 'pending',
                    'qr_redeem' => Str::random(15),
                ]);

                if ($isGuest) {
                    GuestBookingAccess::grant($booking);
                }

                // Mark promo as used jika ada (hanya user login)
                if ($promoId && Auth::check()) {
                    $promo = Promo::find($promoId);
                    if ($promo) {
                        $promo->recordUsage(Auth::id(), $booking->id);
                    }
                }

                // 3. UBAH STATE & SET TIMER
                foreach ($seats as $seat) {
                    TicketBooking::create([
                        'booking_id' => $booking->id,
                        'schedule_id' => $schedule->id,
                        'seat_id' => $seat->id,
                        'price_at_sale' => $schedule->ticket_price,
                    ]);

                    $seat->update([
<<<<<<< Updated upstream
                         'status' => 'pending',
                         'locked_until' => now()->addMinutes(5),

                         // guest tidak punya user_id
                         'locked_by_user_id' => Auth::check()
                            ? Auth::id()
                            : null
=======
                        'status' => 'pending',
                        'locked_until' => now()->addMinutes(5),
                        'locked_by_user_id' => Auth::id(),
>>>>>>> Stashed changes
                    ]);

                    // 4. OBSERVER PATTERN: Broadcast agar layar user lain jadi abu-abu
                    try {
                        broadcast(new \App\Events\SeatStatusUpdated($seat->id, 'pending'))->toOthers();
                    } catch (\Exception $e) {
                        // Abaikan error Pusher agar transaksi tetap lanjut
                        \Illuminate\Support\Facades\Log::error("Pusher Error (Store): " . $e->getMessage());
                    }
                }

                return $booking;
            }, 3);

<<<<<<< Updated upstream
            //SIMPAN SESSION KHUSUS GUEST
            if (!Auth::check()) {
                session([
                    'guest_booking_id' => $booking->id
                ]);
            }

            return redirect()->route('booking.payment', $booking)
=======
            return redirect()->route('booking.payment', $this->bookingRouteParams($booking))
>>>>>>> Stashed changes
                ->with('success', 'Kursi berhasil di-lock sementara. Segera lakukan pembayaran dalam 5 menit.');

        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Tampilkan pilihan metode pembayaran (Strategy Pattern)
     */
    public function payment(Request $request, Booking $booking)
    {
<<<<<<< Updated upstream
       // Cek Hak Akses
        $isAuthorized = false;
        if (Auth::check() && $booking->user_id === Auth::id()) {
            $isAuthorized = true;
        } elseif (!Auth::check() && session('guest_booking_id') === $booking->id) {
            $isAuthorized = true;
        }

        if (!$isAuthorized) {
            abort(403, 'Anda tidak memiliki akses ke halaman transaksi ini.');
        }

        if ($booking->status === 'confirmed') {
            return redirect()->route('booking.confirmation', $booking);
=======
        GuestBookingAccess::authorize($booking, $request);

        if ($booking->status === 'confirmed') {
            return $this->redirectAfterConfirmed($booking);
>>>>>>> Stashed changes
        }

        $booking->load(['ticketBookings.seat', 'ticketBookings.schedule.film', 'promo']);
        $paymentMethods = PaymentContext::availableMethods();
        $isGuest = GuestBookingAccess::isGuestBooking($booking);

        return view('booking.payment', compact('booking', 'paymentMethods', 'isGuest'));
    }

    /**
     * Initiate payment dengan metode yang dipilih (Strategy Pattern)
     * Setiap kali dipanggil, buat Payment baru dengan status pending
     */
    public function initiatePayment(Request $request, Booking $booking)
    {
<<<<<<< Updated upstream
       // Cek Hak Akses
        $isAuthorized = false;
        if (Auth::check() && $booking->user_id === Auth::id()) {
            $isAuthorized = true;
        } elseif (!Auth::check() && session('guest_booking_id') === $booking->id) {
            $isAuthorized = true;
        }

        if (!$isAuthorized) {
            abort(403, 'Anda tidak memiliki akses ke halaman transaksi ini.');
        }
=======
        GuestBookingAccess::authorize($booking, $request);
>>>>>>> Stashed changes

        $validated = $request->validate([
            'payment_method' => 'required|in:qris,virtual_account',
        ]);

        try {
            // Resolve strategy berdasarkan metode
            $strategy = PaymentContext::resolve($validated['payment_method']);

            // Buat payment baru (selalu buat baru dengan status pending)
            $payment = $strategy->initiate($booking);

            return redirect()->route('booking.process-payment', array_merge(
                $this->bookingRouteParams($booking),
                ['payment' => $payment]
            ));

        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal memulai pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan halaman proses pembayaran (QR code / VA number + countdown)
     */
    public function processPayment(Request $request, Booking $booking, Payment $payment)
    {
<<<<<<< Updated upstream
       // Cek Hak Akses
        $isAuthorized = false;
        if (Auth::check() && $booking->user_id === Auth::id()) {
            $isAuthorized = true;
        } elseif (!Auth::check() && session('guest_booking_id') === $booking->id) {
            $isAuthorized = true;
        }

        if (!$isAuthorized) {
            abort(403, 'Anda tidak memiliki akses ke halaman transaksi ini.');
        }
=======
        GuestBookingAccess::authorize($booking, $request);
>>>>>>> Stashed changes

        if ($payment->booking_id !== $booking->id) {
            abort(404);
        }

        // Jika payment sudah expired, mark as failed
        if ($payment->status === 'pending' && $payment->isExpired()) {
            $payment->markAsFailed();
            return redirect()->route('booking.payment', $this->bookingRouteParams($booking))
                ->with('error', 'Waktu pembayaran telah habis. Silakan coba lagi.');
        }

        // Jika sudah success, redirect ke confirmation
        if ($payment->status === 'success') {
<<<<<<< Updated upstream
            return redirect()->route('booking.confirmation', $booking);
=======
            return $this->redirectAfterConfirmed($booking);
>>>>>>> Stashed changes
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
<<<<<<< Updated upstream
        // Cek Hak Akses
        $isAuthorized = false;
        if (Auth::check() && $booking->user_id === Auth::id()) {
            $isAuthorized = true;
        } elseif (!Auth::check() && session('guest_booking_id') === $booking->id) {
            $isAuthorized = true;
        }

        if (!$isAuthorized) {
            abort(403, 'Anda tidak memiliki akses ke halaman transaksi ini.');
        }
=======
        GuestBookingAccess::authorize($booking, $request);
>>>>>>> Stashed changes

        if ($payment->booking_id !== $booking->id) {
            abort(404);
        }

        // Cek apakah payment masih pending
        if ($payment->status !== 'pending') {
            return redirect()->route('booking.payment', $booking)
                ->with('error', 'Payment ini sudah diproses sebelumnya.');
        }

<<<<<<< Updated upstream
        // Cek expired
        if ($payment->isExpired()) {
            $payment->markAsFailed();
            return redirect()->route('booking.payment', $booking)
                ->with('error', 'Waktu pembayaran telah habis. Silakan coba lagi.');
        }
=======
            if (!$validation['valid']) {
                return redirect()->route('booking.payment', $this->bookingRouteParams($booking))
                    ->with('error', $validation['message']);
            }

            // Jika ada warning, tampilkan tapi tetap lanjut
            $warning = $validation['warning'] ?? null;
>>>>>>> Stashed changes

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
                    try {
                        broadcast(new \App\Events\SeatStatusUpdated($seat->id, 'booked'))->toOthers();
                    } catch (\Exception $e) {
                        // Abaikan error Pusher agar transaksi tetap lanjut
                        \Illuminate\Support\Facades\Log::error("Pusher Error (Confirm): " . $e->getMessage());
                    }
                }
            });

<<<<<<< Updated upstream
            return redirect()->route('booking.confirmation', $booking)
                ->with('success', 'Pembayaran berhasil! Tiket Anda telah dikonfirmasi.');
=======
            $this->sendTicketEmail($booking->fresh());

            return $this->redirectAfterConfirmed($booking, 'Pembayaran berhasil! Tiket Anda telah dikonfirmasi.');
>>>>>>> Stashed changes

        } catch (\Throwable $e) {
            $payment->markAsFailed();
            return redirect()->route('booking.payment', $this->bookingRouteParams($booking))
                ->with('error', 'Pembayaran gagal: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan confirmation ticket
     */
    public function confirmation(Request $request, Booking $booking)
    {
<<<<<<< Updated upstream
        // Cek Hak Akses
        $isAuthorized = false;
        if (Auth::check() && $booking->user_id === Auth::id()) {
            $isAuthorized = true;
        } elseif (!Auth::check() && session('guest_booking_id') === $booking->id) {
            $isAuthorized = true;
        }

        if (!$isAuthorized) {
            abort(403, 'Anda tidak memiliki akses ke halaman transaksi ini.');
        }

        $booking->load(['ticketBookings.seat', 'ticketBookings.schedule.film', 'ticketBookings.schedule.studio', 'user', 'payments']);

        return view('booking.confirmation', compact('booking'));
=======
        GuestBookingAccess::authorize($booking, $request);

        return $this->redirectAfterConfirmed($booking);
    }

    /**
     * Halaman tiket untuk guest (akses via session atau token di URL)
     */
    public function guestTicket(Request $request, Booking $booking)
    {
        GuestBookingAccess::authorize($booking, $request);

        if ($booking->status !== 'confirmed') {
            return redirect()->route('booking.payment', $this->bookingRouteParams($booking))
                ->with('error', 'Pembayaran belum selesai. Selesaikan pembayaran terlebih dahulu.');
        }

        $booking->load(['ticketBookings.seat', 'ticketBookings.schedule.film', 'ticketBookings.schedule.studio', 'promo']);

        return view('booking.guest-ticket', compact('booking'));
>>>>>>> Stashed changes
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
    public function getBookingDetails(Request $request, Booking $booking)
    {
        GuestBookingAccess::authorize($booking, $request);

        return response()->json(
            $booking->load('ticketBookings.seat', 'ticketBookings.schedule', 'payments', 'promo')
        );
    }

    /**
     * Check payment status (AJAX - untuk auto-check dari frontend)
     */
    public function checkPaymentStatus(Request $request, Payment $payment)
    {
        GuestBookingAccess::authorize($payment->booking, $request);

        return response()->json([
            'status' => $payment->status,
            'remaining_seconds' => $payment->remaining_seconds,
            'is_expired' => $payment->isExpired(),
        ]);
    }
<<<<<<< Updated upstream
=======

    /**
     * Store customer review from transaction history
     */
    public function storeReview(Request $request)
    {
        $validated = $request->validate([
            'film_id' => 'required|exists:films,id',
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $filmId = $validated['film_id'];
        $userId = Auth::id();

        // 1. Verify user has actually bought and watched the movie (check by schedule date, not status)
        $hasBoughtAndWatched = Booking::where('user_id', $userId)
            ->where('status', 'confirmed')
            ->whereHas('ticketBookings.schedule', function ($q) use ($filmId) {
                $q->where('film_id', $filmId)
                  ->where('schedule_date', '<=', now()->toDateString());
            })
            ->exists();

        if (!$hasBoughtAndWatched) {
            return back()->with('error', 'Anda hanya dapat memberikan ulasan untuk film yang tiketnya sudah dibeli dan jadwal tayangnya telah selesai.');
        }

        // 2. Prevent duplicate reviews for the same film by the same user
        $existingReview = Review::where('user_id', $userId)
            ->where('film_id', $filmId)
            ->first();

        if ($existingReview) {
            return back()->with('error', 'Anda sudah memberikan ulasan untuk film ini.');
        }

        // 3. Create review
        Review::create([
            'user_id' => $userId,
            'film_id' => $filmId,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? '',
        ]);

        return back()->with('success', 'Ulasan Anda berhasil dikirim! Terima kasih atas masukan Anda.');
    }

    private function redirectAfterConfirmed(Booking $booking, ?string $message = null)
    {
        if (GuestBookingAccess::isGuestBooking($booking)) {
            return redirect()->route('booking.guest-ticket', [
                'booking' => $booking->id,
                'token' => $booking->access_token,
            ])->with('success', $message ?? 'Pembayaran berhasil! Tiket telah dikirim ke email Anda.');
        }

        return redirect()->route('booking.tickets')
            ->with('success_booking', $booking->id)
            ->with('success', $message ?? 'Pembayaran berhasil!');
    }

    private function bookingRouteParams(Booking $booking): array
    {
        $params = ['booking' => $booking];

        if (GuestBookingAccess::isGuestBooking($booking) && $booking->access_token) {
            $params['token'] = $booking->access_token;
        }

        return $params;
    }

    private function sendTicketEmail(Booking $booking): void
    {
        if ($booking->status !== 'confirmed') {
            return;
        }

        $email = $booking->customerEmail();
        if (!$email) {
            return;
        }

        try {
            Mail::to($email)->send(new TicketConfirmationMail($booking));
        } catch (\Throwable $e) {
            Log::error('Gagal kirim email tiket: ' . $e->getMessage(), [
                'booking_id' => $booking->id,
                'email' => $email,
            ]);
        }
    }
>>>>>>> Stashed changes
}
