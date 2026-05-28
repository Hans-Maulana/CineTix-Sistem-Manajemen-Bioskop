<?php

namespace App\Services\ChainOfResponsibility\BookingApproval;

use Illuminate\Support\Facades\Auth;
use App\Services\ChainOfResponsibility\BookingApprovalHandler;

class UserAuthorizationHandler extends BookingApprovalHandler
{
    /**
     * User login: set user_id. Guest: wajib guest_email, user_id null.
     */
    protected function approve(array $bookingData): array
    {
        if (Auth::check()) {
            $bookingData['user_id'] = Auth::id();
            $bookingData['guest_email'] = null;

            return [
                'approved' => true,
                'message' => 'User authorized',
                'booking_data' => $bookingData,
            ];
        }

        $email = strtolower(trim($bookingData['guest_email'] ?? ''));

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->reject('Email wajib diisi dengan format yang valid untuk pengiriman tiket.');
        }

        $bookingData['user_id'] = null;
        $bookingData['guest_email'] = $email;

        return [
            'approved' => true,
            'message' => 'Guest authorized',
            'booking_data' => $bookingData,
        ];
    }
}
