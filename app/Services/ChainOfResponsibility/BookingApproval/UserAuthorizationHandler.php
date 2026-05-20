<?php

namespace App\Services\ChainOfResponsibility\BookingApproval;

use Illuminate\Support\Facades\Auth;
use App\Services\ChainOfResponsibility\BookingApprovalHandler;

class UserAuthorizationHandler extends BookingApprovalHandler
{
    /**
     * Validasi: apakah user authenticated?
     * Hanya user yang sudah login yang bisa booking
     */
    protected function approve(array $bookingData): array
    {
        if (!Auth::check()) {
            return $this->reject('Anda harus login terlebih dahulu untuk melakukan pemesanan.');
        }

        // Tambahkan user ID ke booking data
        $bookingData['user_id'] = Auth::id();

        return [
            'approved' => true,
            'message' => 'User authorized',
            'booking_data' => $bookingData,
        ];
    }
}
