<?php

namespace App\Support;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuestBookingAccess
{
    public static function grant(Booking $booking): void
    {
        if ($booking->user_id === null && $booking->access_token) {
            session(["guest_booking.{$booking->id}" => $booking->access_token]);
        }
    }

    public static function canAccess(Booking $booking, ?Request $request = null): bool
    {
        if (Auth::check() && $booking->user_id === Auth::id()) {
            return true;
        }

        if ($booking->user_id !== null) {
            return false;
        }

        $token = $request?->query('token') ?? session("guest_booking.{$booking->id}");

        if ($token && $booking->access_token && hash_equals($booking->access_token, $token)) {
            self::grant($booking);

            return true;
        }

        return false;
    }

    public static function authorize(Booking $booking, ?Request $request = null): void
    {
        if (!self::canAccess($booking, $request)) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }
    }

    public static function isGuestBooking(Booking $booking): bool
    {
        return $booking->user_id === null && filled($booking->guest_email);
    }
}
