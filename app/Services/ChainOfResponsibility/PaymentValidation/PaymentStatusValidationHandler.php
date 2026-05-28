<?php

namespace App\Services\ChainOfResponsibility\PaymentValidation;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\ChainOfResponsibility\PaymentValidationHandler;

class PaymentStatusValidationHandler extends PaymentValidationHandler
{
    /**
     * Validasi: apakah payment status masih 'pending'?
     * Status lain (success, failed) berarti sudah di-process
     */
    protected function validate(Booking $booking, Payment $payment): array
    {
        if ($payment->status !== 'pending') {
            return $this->error(
                'Payment ini sudah di-proses sebelumnya dengan status: ' . 
                $payment->status_label . 
                '. Tidak bisa diproses lagi.'
            );
        }

        return $this->success();
    }
}
