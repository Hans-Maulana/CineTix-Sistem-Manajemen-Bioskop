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
}
