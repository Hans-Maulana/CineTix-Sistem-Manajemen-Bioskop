<?php

namespace App\Observers;

use App\Models\Seat;
use App\Events\SeatBooked;
use App\Events\SeatAvailable;

class SeatObserver
{
    /**
     * Handle the Seat "created" event.
     */
    public function created(Seat $seat): void
    {
        //
    }

    /**
     * Handle the Seat "updated" event.
     */
    public function updated(Seat $seat): void
    {
        // Check if status was changed
        if ($seat->isDirty('status')) {
            $newStatus = $seat->status;
            $oldStatus = $seat->getOriginal('status');

            // Broadcast appropriate event
            if ($newStatus === 'occupied' && $oldStatus === 'available') {
                // Seat was booked. Find the most recent ticket booking for this seat
                $ticket = $seat->ticketBookings()->with('schedule')->latest()->first();
                if ($ticket && $ticket->schedule) {
                    broadcast(new SeatBooked($seat, $ticket->schedule))->toOthers();
                }
            } elseif ($newStatus === 'available' && $oldStatus === 'occupied') {
                // Seat was freed up. Find the most recent ticket booking for this seat
                $ticket = $seat->ticketBookings()->with('schedule')->latest()->first();
                if ($ticket && $ticket->schedule) {
                    broadcast(new SeatAvailable($seat, $ticket->schedule))->toOthers();
                }
            }
        }
    }

    /**
     * Handle the Seat "deleted" event.
     */
    public function deleted(Seat $seat): void
    {
        //
    }

    /**
     * Handle the Seat "restored" event.
     */
    public function restored(Seat $seat): void
    {
        //
    }

    /**
     * Handle the Seat "force deleted" event.
     */
    public function forceDeleted(Seat $seat): void
    {
        //
    }
}
