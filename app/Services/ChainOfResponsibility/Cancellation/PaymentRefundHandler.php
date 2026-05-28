<?php

namespace App\Services\ChainOfResponsibility\Cancellation;

use App\Models\Booking;
use App\Services\ChainOfResponsibility\CancellationHandler;

class PaymentRefundHandler extends CancellationHandler
{
    /**
     * Validasi: apakah ada payment pending yang perlu di-refund?
     * Jika ada, warning untuk user
     */
    protected function canCancel(Booking $booking): array
    {
        $pendingPayments = $booking->payments()
            ->where('status', 'pending')
            ->get();

        if ($pendingPayments->isNotEmpty()) {
            // Log warning tapi tetap izinkan cancellation
            $paymentCount = $pendingPayments->count();
            
            // Store payment info untuk diproses di step selanjutnya
            return [
                'allowed' => true,
                'message' => 'OK',
                'warning' => "Ada $paymentCount payment pending yang akan di-mark sebagai failed.",
                'pending_payments' => $pendingPayments,
            ];
        }

        return $this->allowed();
    }
}
