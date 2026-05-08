<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BundlingBooking extends Model
{
    use HasFactory;

    protected $table = 'bundling_bookings';

    protected $fillable = [
        'booking_id',
        'bundling_id',
    ];



    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function bundling()
    {
        return $this->belongsTo(Bundling::class);
    }
}
