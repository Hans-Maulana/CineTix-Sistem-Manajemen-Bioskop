<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'film_id',
        'studio_id',
        'schedule_date',
        'start_time',
        'end_time',
        'ticket_price',
    ];

    protected $casts = [
        'schedule_date' => 'date',
        'start_time'    => 'datetime:H:i',
        'end_time'      => 'datetime:H:i',
        'ticket_price'  => 'decimal:2',
    ];



    public function film()
    {
        return $this->belongsTo(Film::class);
    }

    public function studio()
    {
        return $this->belongsTo(Studio::class);
    }

    public function ticketBookings()
    {
        return $this->hasMany(TicketBooking::class);
    }
}
