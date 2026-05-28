<?php

namespace App\Services\ChainOfResponsibility\PaymentValidation;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\ChainOfResponsibility\PaymentValidationHandler;

class PaymentTimeoutValidationHandler extends PaymentValidationHandler
{
    /**
     * Validasi: apakah payment masih dalam countdown window?
     * Jika sudah expired, reject
     */
    protected function validate(Booking $booking, Payment $payment): array
    {
        // Check apakah payment sudah expired
        if ($payment->isExpired()) {
            // Auto mark sebagai failed - akan trigger PaymentObserver
            $payment->markAsFailed();
            
            return $this->error(
                'Waktu pembayaran telah habis (' . 
                ($payment->countdown_seconds / 60) . 
                ' menit). Silakan membuat pembayaran baru.'
            );
        }

        // Get remaining seconds
        $remainingSeconds = $payment->remaining_seconds;
        
        // Warning jika kurang dari 1 menit
        if ($remainingSeconds < 60) {
            return [
                'valid' => true,
                'message' => 'OK',
                'warning' => 'Sisa waktu pembayaran kurang dari 1 menit!',
            ];
        }

        return $this->success();
    }
}
