<?php

namespace App\Services\ChainOfResponsibility\Cancellation;

use App\Models\Booking;
use App\Models\TicketBooking;
use App\Services\ChainOfResponsibility\CancellationHandler;

class LockedSeatsHandler extends CancellationHandler
{
    /**
     * Validasi: apakah ada kursi yang terkait dengan booking?
     * Jika tidak ada kursi, berarti booking malformed
     */
    protected function canCancel(Booking $booking): array
    {
        $ticketBookings = $booking->ticketBookings()->get();

        if ($ticketBookings->isEmpty()) {
            return $this->denied(
                'Booking ini tidak memiliki kursi yang terkait. Tidak bisa dibatalkan.'
            );
        }

        // Check apakah ada kursi yang locked
        $lockedSeats = $ticketBookings->filter(function ($ticket) {
            return $ticket->seat && $ticket->seat->status === 'pending';
        });

        if ($lockedSeats->isEmpty()) {
            return $this->denied(
                'Tidak ada kursi yang di-lock untuk booking ini. Mungkin kursi sudah dibebaskan.'
            );
        }

        return $this->allowed();
    }
}
