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
            ->whereHas('booking', fn ($q) => $q->where('status', '!=', 'cancelled'))
            ->where('schedule_id', $scheduleId)
            ->exists();
    }

    // implementasi State Pattern
    public function isAvailable(): bool
    {
        // Tersedia jika statusnya 'available'
        if ($this->status === 'available') {
            return true;
        }

        // jika statusnya 'pending' tapi waktu lock-nya sudah lewat
        if ($this->status === 'pending' && $this->locked_until && $this->locked_until->isPast()) {
            return true;
        }

        return false;
    }
}
