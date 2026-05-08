<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TicketBooking extends Model
{
    use HasFactory;

    protected $table = 'ticket_bookings';

    protected $fillable = [
        'booking_id',
        'schedule_id',
        'seat_id',
        'price_at_sale',
    ];

    protected $casts = [
        'price_at_sale' => 'decimal:2',
    ];

    // -------------------------
    // Relationships
    // -------------------------

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function seat()
    {
        return $this->belongsTo(Seat::class);
    }
}
