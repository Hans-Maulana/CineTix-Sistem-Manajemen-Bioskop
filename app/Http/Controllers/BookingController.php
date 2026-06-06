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

        $schedule->load('film', 'studio.seats');

        $bookedSeatIds = TicketBooking::whereHas('booking', function ($q) {
            $q->whereIn('status', ['pending', 'confirmed']);
        })
            ->where('schedule_id', $schedule->id)
            ->pluck('seat_id')
            ->toArray();

        $seatsByCode = $schedule->studio->seats->keyBy('seat_code');
        $isAuthenticated = Auth::check();
        $user = Auth::user();

        return view('booking.show', compact('schedule', 'bookedSeatIds', 'seatsByCode', 'isAuthenticated', 'user'));
    }

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
            'guest_email' => 'required|email:rfc,filter|max:255',
        ];

        $validated = $request->validate($rules);

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
        $paymentMethods = PaymentContext::availableMethods();
        $isGuest = GuestBookingAccess::isGuestBooking($booking);

        return view('booking.payment', compact('booking', 'paymentMethods', 'isGuest'));
    }

    public function initiatePayment(Request $request, Booking $booking)
    {
        GuestBookingAccess::authorize($booking, $request);

        $validated = $request->validate([
            'payment_method' => 'required|in:qris,virtual_account',
        ]);

        try {
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

        $email = $request->email;
        $otp = rand(100000, 999999);


        Cache::put('guest_otp_' . $email, $otp, now()->addMinutes(5));

    
        Mail::to($email)->send(new GuestOtpMail($otp));

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

        $email = $request->email;
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

        try {
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

            $this->sendTicketEmail($booking->fresh());

            return $this->redirectAfterConfirmed($booking, 'Pembayaran berhasil! Tiket Anda telah dikonfirmasi.');
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

        if ($booking->status !== 'confirmed') {
            return redirect()->route('booking.payment', $this->bookingRouteParams($booking))
                ->with('error', 'Pembayaran belum selesai. Selesaikan pembayaran terlebih dahulu.');
        }

        $booking->load(['ticketBookings.seat', 'ticketBookings.schedule.film', 'ticketBookings.schedule.studio', 'promo']);

        return view('booking.guest-ticket', compact('booking'));
    }

    public function tickets()
    {
        $bookings = Auth::user()->bookings()
            ->where('status', 'confirmed')
            ->whereHas('ticketBookings.schedule', function ($q) {
                $q->where('status', '!=', 'complete')
                  ->where('schedule_date', '>', now());
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
}
