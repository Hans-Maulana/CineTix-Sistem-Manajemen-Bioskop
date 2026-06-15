<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Seat extends Model
{
    use HasFactory;

    protected $fillable = [
        'studio_id',
        'row_label',
        'seat_number',
        'seat_code',
        'status',
        'locked_until',
        'locked_by_user_id'
    ];

    protected $casts = [
        'locked_until' => 'datetime'
    ];


    public function studio()
    {
        return $this->belongsTo(Studio::class);
    }

    public function ticketBookings()
    {
        return $this->hasMany(TicketBooking::class);
    }



    public function checkAvailability(int $scheduleId): bool
    {
        return !$this->ticketBookings()
            ->whereHas('booking', fn ($q) => $q->whereNotIn('status', ['cancelled', 'refunded']))
            ->where('schedule_id', $scheduleId)
            ->exists();
    }

    // implementasi State Pattern
    public function isAvailable(?int $scheduleId = null): bool
    {
        // Jika statusnya 'pending' dan lock masih aktif oleh user lain
        if ($this->status === 'pending' && $this->locked_until && $this->locked_until->isFuture()) {
            return false;
        }

        // Jika scheduleId dispesifikasikan, cek ketersediaan riil untuk schedule tersebut
        if ($scheduleId) {
            return $this->checkAvailability($scheduleId);
        }

        // Fallback jika tidak ada scheduleId, gunakan status global
        if ($this->status === 'booked') {
            return false;
        }

        return true;
    }
}
