<?php

namespace App\Services\ChainOfResponsibility\PaymentValidation;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\ChainOfResponsibility\PaymentValidationHandler;

class BookingStatusValidationHandler extends PaymentValidationHandler
{
    /**
     * Validasi: apakah booking status masih 'pending'?
     * Hanya booking pending yang bisa di-confirm ke booked
     */
    protected function validate(Booking $booking, Payment $payment): array
    {
        if ($booking->status !== 'pending') {
            return $this->error(
                'Booking ini sudah di-proses sebelumnya dengan status: ' . 
                $booking->status . 
                '. Tidak bisa diproses lagi.'
            );
        }

        return $this->success();
    }
}
