<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Booking;

class BookingPolicy
{
    /**
     * Determine if the user can view the booking
     */
    public function view(User $user, Booking $booking): bool
    {
        return $user->id === $booking->user_id;
    }

    /**
     * Determine if the user can create a booking
     */
    public function create(User $user): bool
    {
        return $user->role->name === 'customer';
    }

    /**
     * Determine if the user can update the booking
     */
    public function update(User $user, Booking $booking): bool
    {
        return $user->id === $booking->user_id;
    }

    /**
     * Determine if the user can delete the booking
     */
    public function delete(User $user, Booking $booking): bool
    {
        return $user->id === $booking->user_id && $booking->status === 'pending';
    }
}
