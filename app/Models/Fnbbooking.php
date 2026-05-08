<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FnbBooking extends Model
{
    use HasFactory;

    protected $table = 'fnb_bookings';

    protected $fillable = [
        'booking_id',
        'fnb_id',
        'quantity',
        'price_at_sale',
    ];

    protected $casts = [
        'quantity'      => 'integer',
        'price_at_sale' => 'decimal:2',
    ];

    

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function fnb()
    {
        return $this->belongsTo(Fnb::class, 'fnb_id');
    }



    public function subtotal(): float
    {
        return $this->price_at_sale * $this->quantity;
    }
}
