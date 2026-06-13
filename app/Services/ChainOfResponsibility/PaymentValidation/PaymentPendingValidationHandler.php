<?php

namespace App\Services\ChainOfResponsibility\PaymentValidation;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\ChainOfResponsibility\PaymentValidationHandler;

class PaymentPendingValidationHandler extends PaymentValidationHandler
{
    /**
     * Validasi: apakah ada payment pending untuk booking ini?
     * Jika ada lebih dari satu payment pending, reject
     */
    protected function validate(Booking $booking, Payment $payment): array
    {
        // Check apakah payment ini benar-benar terkait dengan booking
        if ($payment->booking_id !== $booking->id) {
            return $this->error('Payment tidak terkait dengan booking ini.');
        }

        // Lepaskan payment pending expired, lalu tolak jika masih ada pending aktif lain
        Payment::failExpiredPendingForBooking($booking->id);

        $otherPendingPayments = Payment::where('booking_id', $booking->id)
            ->where('status', 'pending')
            ->where('id', '!=', $payment->id)
            ->count();

        if ($otherPendingPayments > 0) {
            return $this->error('Sudah ada payment pending lain untuk booking ini. Silakan selesaikan payment sebelumnya.');
        }

        return $this->success();
    }
}
