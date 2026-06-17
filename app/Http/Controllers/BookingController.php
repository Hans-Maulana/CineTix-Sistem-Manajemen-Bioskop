<?php

namespace App\Http\Controllers;

use App\Mail\TicketConfirmationMail;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Promo;
use App\Models\Review;
use App\Models\Schedule;
use App\Models\Seat;
use App\Models\TicketBooking;
use App\Services\ChainOfResponsibility\BookingApprovalChain;
use App\Services\ChainOfResponsibility\PaymentValidationChain;
use App\Services\Payment\PaymentContext;
use App\Support\GuestBookingAccess;
use Illuminate\Support\Facades\Cache;
use App\Mail\GuestOtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function show(Schedule $schedule)
    {
        $now = \Carbon\Carbon::now();
        $startDateTime = \Carbon\Carbon::parse($schedule->schedule_date->format('Y-m-d') . ' ' . $schedule->start_time->format('H:i:s'));
        if ($schedule->status === 'complete' || $schedule->status === 'canceled' || $now->greaterThanOrEqualTo($startDateTime)) {
            return redirect()->route('landing-page')->with('error', 'Maaf, jadwal tayang ini sudah lewat.');
        }

        if (! $schedule->isWithinBookingWindow($now)) {
            return redirect()
                ->route('films.detail', $schedule->film_id)
                ->with('error', 'Penjualan tiket untuk jadwal ini belum dibuka. Tiket hanya tersedia hingga ' . Schedule::BOOKING_WINDOW_DAYS . ' hari sebelum tayang.');
        }

        $schedule->load('film', 'studio.seats');

        $isAuthenticated = Auth::check();
        $user = Auth::user();

        // Jika user kembali ke halaman pemilihan kursi, batalkan booking pending mereka sebelumnya
        // agar kursi tidak terkunci oleh sesi mereka sendiri.
        if ($isAuthenticated) {
            $pendingBookings = \App\Models\Booking::where('user_id', $user->id)
                ->where('schedule_id', $schedule->id)
                ->where('status', 'pending')
                ->get();

            foreach ($pendingBookings as $pb) {
                // Lepas lock pada kursi-kursi yang terkait dengan booking ini
                $seatIds = $pb->ticketBookings()->pluck('seat_id')->toArray();
                if (!empty($seatIds)) {
                    \App\Models\Seat::whereIn('id', $seatIds)->update([
                        'status' => 'available',
                        'locked_until' => null,
                        'locked_by_user_id' => null,
                    ]);
                }
                
                $pb->update(['status' => 'cancelled']);
                if ($pb->payment) {
                    $pb->payment->update(['status' => 'failed']);
                }
            }
        } else {
            // Untuk guest, cek pending booking berdasarkan session
            $guestEmail = session('guest_email');
            if ($guestEmail) {
                $pendingBookings = \App\Models\Booking::where('guest_email', $guestEmail)
                    ->where('schedule_id', $schedule->id)
                    ->where('status', 'pending')
                    ->get();
                    
                foreach ($pendingBookings as $pb) {
                    $seatIds = $pb->ticketBookings()->pluck('seat_id')->toArray();
                    if (!empty($seatIds)) {
                        \App\Models\Seat::whereIn('id', $seatIds)->update([
                            'status' => 'available',
                            'locked_until' => null,
                            'locked_by_user_id' => null,
                        ]);
                    }

                    $pb->update(['status' => 'cancelled']);
                    if ($pb->payment) {
                        $pb->payment->update(['status' => 'failed']);
                    }
                }
            }
        }

        $bookedSeatIds = TicketBooking::whereHas('booking', function ($q) {
            $q->whereNotIn('status', ['cancelled', 'refunded']);
        })
            ->where('schedule_id', $schedule->id)
            ->pluck('seat_id')
            ->toArray();

        $seatsByCode = $schedule->studio->seats->keyBy('seat_code');

        $restoredSeats = $this->pullPendingSeatSelection($schedule, $bookedSeatIds);

        return view('booking.show', compact('schedule', 'bookedSeatIds', 'seatsByCode', 'isAuthenticated', 'user', 'restoredSeats'));
    }

    /**
     * Simpan pilihan kursi sementara (mis. sebelum login) agar tetap ada setelah auth.
     */
    public function rememberSeats(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'seat_ids' => 'required|array|min:1',
            'seat_ids.*' => 'integer|exists:seats,id',
        ]);

        $validSeatIds = Seat::whereIn('id', $validated['seat_ids'])
            ->where('studio_id', $schedule->studio_id)
            ->pluck('id')
            ->all();

        if (empty($validSeatIds)) {
            return response()->json(['success' => false, 'message' => 'Kursi tidak valid.'], 422);
        }

        session([
            'pending_seat_selection.' . $schedule->id => [
                'seat_ids' => array_values($validSeatIds),
                'saved_at' => now()->timestamp,
            ],
            'seat_restore_notice.' . $schedule->id => true,
        ]);

        return response()->json(['success' => true]);
    }

    private function pullPendingSeatSelection(Schedule $schedule, array $bookedSeatIds): array
    {
        $key = 'pending_seat_selection.' . $schedule->id;
        $pending = session($key);

        if (! is_array($pending) || empty($pending['seat_ids'])) {
            return [];
        }

        $savedAt = (int) ($pending['saved_at'] ?? 0);
        if ($savedAt < now()->subHours(2)->timestamp) {
            session()->forget($key);

            return [];
        }

        $restored = $schedule->studio->seats
            ->whereIn('id', $pending['seat_ids'])
            ->reject(fn ($seat) => in_array($seat->id, $bookedSeatIds, true))
            ->map(fn ($seat) => ['id' => $seat->id, 'code' => $seat->seat_code])
            ->values()
            ->all();

        if (! empty($restored) && session()->pull('seat_restore_notice.' . $schedule->id)) {
            session()->flash('seats_restored', count($restored));
        }

        return $restored;
    }

    public function store(Request $request)
    {
        // Normalisasi seat_ids: bisa berupa string CSV (fallback) atau array langsung
        if (is_string($request->seat_ids)) {
            $request->merge(['seat_ids' => array_filter(explode(',', $request->seat_ids))]);
        } elseif (is_array($request->seat_ids) && count($request->seat_ids) === 1 && is_string($request->seat_ids[0]) && str_contains($request->seat_ids[0], ',')) {
            $request->merge(['seat_ids' => array_filter(explode(',', $request->seat_ids[0]))]);
        }

        $rules = [
            'schedule_id' => 'required|exists:schedules,id',
            'seat_ids' => 'required|array|min:1',
            'seat_ids.*' => 'exists:seats,id',
            'promo_code' => 'nullable|string',
            'guest_email' => Auth::check() ? 'nullable|email:rfc,filter|max:255' : 'required|email:rfc,filter|max:255',
        ];

        $validated = $request->validate($rules);

        $scheduleForWindow = Schedule::find($validated['schedule_id']);
        if (! $scheduleForWindow || ! $scheduleForWindow->isWithinBookingWindow()) {
            return back()->with('error', 'Penjualan tiket untuk jadwal ini belum dibuka. Tiket hanya tersedia hingga ' . Schedule::BOOKING_WINDOW_DAYS . ' hari sebelum tayang.');
        }

        try {
            $chain = BookingApprovalChain::build();
            $approval = $chain->handle($validated);

            if (!$approval['approved']) {
                return back()->with('error', $approval['message']);
            }

            $bookingData = $approval['booking_data'] ?? $validated;

            $booking = DB::transaction(function () use ($bookingData) {
                $schedule = Schedule::with('film')->find($bookingData['schedule_id']);
                $seatIds = $bookingData['seat_ids'];

                $seats = Seat::whereIn('id', $seatIds)
                    ->where('studio_id', $schedule->studio_id)
                    ->lockForUpdate()
                    ->get();

                foreach ($seats as $seat) {
                    if (!$seat->isAvailable($schedule->id)) {
                        throw new \Exception("Maaf, kursi {$seat->seat_code} baru saja diambil orang lain.");
                    }
                }

                $totalAmount = $schedule->ticket_price * count($seatIds);
                $promoId = $bookingData['promo_id'] ?? null;

                if (isset($bookingData['discount_amount'])) {
                    $totalAmount -= $bookingData['discount_amount'];
                }

                $isGuest = empty($bookingData['user_id']);

                $booking = Booking::create([
                    'user_id' => $bookingData['user_id'] ?? null,
                    'guest_email' => $bookingData['guest_email'] ?? null,
                    'access_token' => $isGuest ? Str::random(64) : null,
                    'promo_id' => $promoId,
                    'schedule_id' => $bookingData['schedule_id'],
                    'booking_type' => 'ticket',
                    'total_amount' => max(0, $totalAmount),
                    'status' => 'pending',
                    'qr_redeem' => Str::random(15),
                ]);

                if ($isGuest) {
                    GuestBookingAccess::grant($booking);
                    session()->forget('verified_guest_email');
                }

                if ($promoId && Auth::check()) {
                    $promo = Promo::find($promoId);
                    if ($promo) {
                        $promo->recordUsage(Auth::id(), $booking->id);
                    }
                }

                foreach ($seats as $seat) {
                    TicketBooking::create([
                        'booking_id' => $booking->id,
                        'schedule_id' => $schedule->id,
                        'seat_id' => $seat->id,
                        'price_at_sale' => $schedule->ticket_price,
                    ]);

                    $seat->update([
                        'status' => 'pending',
                        'locked_until' => now()->addMinutes(5),
                        'locked_by_user_id' => Auth::id(),
                    ]);

                    try {
                        broadcast(new \App\Events\SeatStatusUpdated($seat->id, 'pending'))->toOthers();
                    } catch (\Exception $e) {
                        Log::error('Pusher Error (Store): ' . $e->getMessage());
                    }
                }

                return $booking;
            }, 3);

            session()->forget('pending_seat_selection.' . $bookingData['schedule_id']);

            return redirect()->route('booking.payment', $this->bookingRouteParams($booking))
                ->with('success', 'Kursi berhasil di-lock sementara. Segera lakukan pembayaran dalam 5 menit.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function payment(Request $request, Booking $booking)
    {
        GuestBookingAccess::authorize($booking, $request);

        if ($booking->status === 'confirmed') {
            return $this->redirectAfterConfirmed($booking);
        }

        $booking->load(['ticketBookings.seat', 'ticketBookings.schedule.film', 'promo']);
        Payment::failExpiredPendingForBooking($booking->id);
        $activePendingPayment = Payment::activePendingForBooking($booking->id);
        $paymentMethods = PaymentContext::availableMethods();
        $isGuest = GuestBookingAccess::isGuestBooking($booking);

        return view('booking.payment', compact('booking', 'paymentMethods', 'isGuest', 'activePendingPayment'));
    }

    public function initiatePayment(Request $request, Booking $booking)
    {
        GuestBookingAccess::authorize($booking, $request);

        $validated = $request->validate([
            'payment_method' => 'required|in:qris,virtual_account',
        ]);

        try {
            Payment::failExpiredPendingForBooking($booking->id);

            $activePending = Payment::activePendingForBooking($booking->id);
            if ($activePending && $activePending->method === $validated['payment_method']) {
                return redirect()->route('booking.process-payment', array_merge(
                    $this->bookingRouteParams($booking),
                    ['payment' => $activePending]
                ));
            }

            Payment::closeOtherPendingForBooking($booking->id);

            $strategy = PaymentContext::resolve($validated['payment_method']);
            $payment = $strategy->initiate($booking);

            return redirect()->route('booking.process-payment', array_merge(
                $this->bookingRouteParams($booking),
                ['payment' => $payment]
            ));
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal memulai pembayaran: ' . $e->getMessage());
        }
    }

    // Fungsi untuk kirim OTP
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $email = strtolower(trim($request->email));
        $otp = rand(100000, 999999);

        Cache::put('guest_otp_' . $email, $otp, now()->addMinutes(5));

        try {
            Mail::to($email)->send(new GuestOtpMail($otp));
        } catch (\Throwable $e) {
            Log::error('Gagal kirim OTP guest: ' . $e->getMessage(), ['email' => $email]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim email OTP. Periksa konfigurasi mail server.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Kode OTP berhasil dikirim ke ' . $email
        ]);
    }

    // verifikasi OTP Guest
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|numeric'
        ]);

        $email = strtolower(trim($request->email));
        $inputOtp = $request->otp;
        $cachedOtp = Cache::get('guest_otp_' . $email);

        if ($cachedOtp && $cachedOtp == $inputOtp) {

            Cache::forget('guest_otp_' . $email);

            session(['verified_guest_email' => $email]);

            return response()->json([
                'success' => true,
                'message' => 'Email berhasil diverifikasi!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Kode OTP salah atau sudah kedaluwarsa.'
        ], 400);
    }

    public function processPayment(Request $request, Booking $booking, Payment $payment)
    {
        GuestBookingAccess::authorize($booking, $request);

        if ($payment->booking_id !== $booking->id) {
            abort(404);
        }

        if ($payment->status === 'pending' && $payment->isExpired()) {
            $payment->markAsFailed();
            return redirect()->route('booking.payment', $this->bookingRouteParams($booking))
                ->with('error', 'Waktu pembayaran telah habis. Silakan coba lagi.');
        }

        if ($payment->status === 'success') {
            return $this->redirectAfterConfirmed($booking);
        }

        $booking->load(['ticketBookings.seat', 'ticketBookings.schedule.film', 'promo']);
        $strategy = PaymentContext::resolve($payment->method);
        $displayData = $strategy->getDisplayData($payment, $booking);

        return view('booking.process-payment', compact('booking', 'payment', 'displayData'));
    }

    public function confirmPayment(Request $request, Booking $booking, Payment $payment)
    {
        GuestBookingAccess::authorize($booking, $request);

        if ($payment->booking_id !== $booking->id) {
            abort(404);
        }

        if ($payment->status === 'success' && $booking->status === 'confirmed') {
            $emailSent = $this->sendTicketEmail($booking->fresh());

            return $this->redirectAfterConfirmed(
                $booking,
                $emailSent
                    ? 'Pembayaran sudah selesai. Tiket telah dikirim ulang ke email Anda.'
                    : 'Pembayaran sudah selesai. Email tiket gagal dikirim — gunakan tombol kirim ulang di halaman tiket.',
                $emailSent
            );
        }

        try {
            Payment::failExpiredPendingForBooking($booking->id);
            Payment::closeOtherPendingForBooking($booking->id, $payment->id);

            $chain = PaymentValidationChain::build();
            $validation = $chain->handle($booking, $payment);

            if (!$validation['valid']) {
                return redirect()->route('booking.payment', $this->bookingRouteParams($booking))
                    ->with('error', $validation['message']);
            }

            DB::transaction(function () use ($payment, $booking) {
                $strategy = PaymentContext::resolve($payment->method);
                $strategy->process($payment);

                $ticketBookings = $booking->ticketBookings()->with('seat')->get();
                foreach ($ticketBookings as $ticket) {
                    $seat = $ticket->seat;
                    if (!$seat) {
                        continue;
                    }
                    $seat->update([
                        'status' => 'booked',
                        'locked_until' => null,
                    ]);

                    try {
                        broadcast(new \App\Events\SeatStatusUpdated($seat->id, 'booked'))->toOthers();
                    } catch (\Exception $e) {
                        Log::error('Pusher Error (Confirm): ' . $e->getMessage());
                    }
                }
            });

            $emailSent = $this->sendTicketEmail($booking);

            $successMessage = $emailSent
                ? 'Pembayaran berhasil! Tiket telah dikirim ke email Anda.'
                : 'Pembayaran berhasil! Tiket siap digunakan. Jika email belum masuk, cek folder spam atau simpan halaman tiket ini.';

            return $this->redirectAfterConfirmed($booking, $successMessage, $emailSent);
        } catch (\Throwable $e) {
            $payment->markAsFailed();
            return redirect()->route('booking.payment', $this->bookingRouteParams($booking))
                ->with('error', 'Pembayaran gagal: ' . $e->getMessage());
        }
    }

    public function confirmation(Request $request, Booking $booking)
    {
        GuestBookingAccess::authorize($booking, $request);

        return $this->redirectAfterConfirmed($booking);
    }

    public function guestTicket(Request $request, Booking $booking)
    {
        GuestBookingAccess::authorize($booking, $request);

        if (!in_array($booking->status, ['confirmed', 'refunded'])) {
            return redirect()->route('booking.payment', $this->bookingRouteParams($booking))
                ->with('error', 'Pembayaran belum selesai. Selesaikan pembayaran terlebih dahulu.');
        }

        $booking->load(['ticketBookings.seat', 'ticketBookings.schedule.film', 'ticketBookings.schedule.studio', 'promo']);

        return view('booking.guest-ticket', compact('booking'));
    }

    public function resendTicketEmail(Request $request, Booking $booking)
    {
        GuestBookingAccess::authorize($booking, $request);

        if ($booking->status !== 'confirmed') {
            return back()->with('error', 'Tiket belum aktif. Selesaikan pembayaran terlebih dahulu.');
        }

        $cacheKey = 'resend_ticket_' . $booking->id;
        $attempts = (int) Cache::get($cacheKey, 0);
        if ($attempts >= 3) {
            return back()->with('error', 'Terlalu banyak permintaan kirim ulang. Coba lagi dalam 1 jam.');
        }
        Cache::put($cacheKey, $attempts + 1, now()->addHour());

        $emailSent = $this->sendTicketEmail($booking);

        if ($emailSent) {
            return back()->with([
                'success' => 'Tiket telah dikirim ulang ke ' . $booking->customerEmail() . '. Periksa inbox atau folder spam.',
                'ticket_email_sent' => true,
            ]);
        }

        return back()->with([
            'error' => 'Gagal mengirim email tiket. Simpan halaman tiket ini sebagai cadangan.',
            'ticket_email_sent' => false,
        ]);
    }

    public function tickets()
    {
        $bookings = Auth::user()->bookings()
            ->where('status', 'confirmed')
            ->whereHas('ticketBookings.schedule', function ($q) {
                $q->where('status', '!=', 'complete')
                  ->whereDate('schedule_date', '>=', now()->toDateString());
            })
            ->with('ticketBookings.schedule.film', 'ticketBookings.schedule.studio', 'payments')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('booking.tickets', compact('bookings'));
    }

    public function history()
    {
        $bookings = Auth::user()->bookings()
            ->with('ticketBookings.schedule.film', 'payments', 'latestPayment')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('booking.history', compact('bookings'));
    }

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
                foreach ($booking->ticketBookings()->with('seat')->get() as $ticket) {
                    $ticket->seat->update(['status' => 'available']);
                }

                $booking->payments()->where('status', 'pending')->update(['status' => 'failed']);
                $booking->update(['status' => 'cancelled']);
            });

            return redirect()->route('booking.history')
                ->with('success', 'Booking berhasil dibatalkan.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal membatalkan booking: ' . $e->getMessage());
        }
    }

    public function getAvailableSeats(Schedule $schedule)
    {
        $bookedSeats = TicketBooking::whereHas('booking', function ($q) {
            $q->whereNotIn('status', ['cancelled', 'refunded']);
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

    public function getBookingDetails(Request $request, Booking $booking)
    {
        GuestBookingAccess::authorize($booking, $request);

        return response()->json(
            $booking->load('ticketBookings.seat', 'ticketBookings.schedule', 'payments', 'promo')
        );
    }

    public function checkPaymentStatus(Request $request, Payment $payment)
    {
        GuestBookingAccess::authorize($payment->booking, $request);

        return response()->json([
            'status' => $payment->status,
            'remaining_seconds' => $payment->remaining_seconds,
            'is_expired' => $payment->isExpired(),
        ]);
    }

    public function storeReview(Request $request)
    {
        $validated = $request->validate([
            'film_id' => 'required|exists:films,id',
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $filmId = $validated['film_id'];
        $userId = Auth::id();

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

        $existingReview = Review::where('user_id', $userId)
            ->where('film_id', $filmId)
            ->first();

        if ($existingReview) {
            return back()->with('error', 'Anda sudah memberikan ulasan untuk film ini.');
        }

        Review::create([
            'user_id' => $userId,
            'film_id' => $filmId,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? '',
        ]);

        return back()->with('success', 'Ulasan Anda berhasil dikirim! Terima kasih atas masukan Anda.');
    }

    private function redirectAfterConfirmed(Booking $booking, ?string $message = null, ?bool $emailSent = null)
    {
        $flash = ['success' => $message ?? 'Pembayaran berhasil!'];

        if ($emailSent !== null) {
            $flash['ticket_email_sent'] = $emailSent;
        }

        if (GuestBookingAccess::isGuestBooking($booking)) {
            return redirect()->route('booking.guest-ticket', [
                'booking' => $booking->id,
                'token' => $booking->access_token,
            ])->with($flash);
        }

        return redirect()->route('booking.tickets')
            ->with(array_merge($flash, ['success_booking' => $booking->id]));
    }

    private function bookingRouteParams(Booking $booking): array
    {
        $params = ['booking' => $booking];

        if (GuestBookingAccess::isGuestBooking($booking) && $booking->access_token) {
            $params['token'] = $booking->access_token;
        }

        return $params;
    }

    private function sendTicketEmail(Booking $booking): bool
    {
        $booking = $booking->fresh()->load([
            'user',
            'ticketBookings.seat',
            'ticketBookings.schedule.film',
            'ticketBookings.schedule.studio',
            'promo',
        ]);

        if ($booking->status !== 'confirmed') {
            Log::warning('Ticket email skipped: booking belum confirmed', [
                'booking_id' => $booking->id,
                'status' => $booking->status,
            ]);

            return false;
        }

        $email = $booking->customerEmail();
        if (!$email) {
            Log::warning('Ticket email skipped: email penerima kosong', [
                'booking_id' => $booking->id,
                'user_id' => $booking->user_id,
            ]);

            return false;
        }

        try {
            Mail::to($email)->send(new TicketConfirmationMail($booking));

            Log::info('Ticket email sent', [
                'booking_id' => $booking->id,
                'email' => $email,
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::error('Gagal kirim email tiket: ' . $e->getMessage(), [
                'booking_id' => $booking->id,
                'email' => $email,
            ]);

            return false;
        }
    }
}
