<?php

namespace App\Services\ChainOfResponsibility\Cancellation;

use App\Models\Booking;
use App\Services\ChainOfResponsibility\CancellationHandler;

class CancellationStatusHandler extends CancellationHandler
{
    /**
     * Validasi: apakah booking status bisa dibatalkan?
     * Hanya status 'pending' yang bisa dibatalkan
     */
    protected function canCancel(Booking $booking): array
    {
        if ($booking->status !== 'pending') {
            return $this->denied(
                'Hanya booking dengan status "pending" yang bisa dibatalkan. ' .
                'Booking ini sudah berstatus: ' . $booking->status
            );
        }

        return $this->allowed();
    }
}
