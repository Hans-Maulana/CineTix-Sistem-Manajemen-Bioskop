<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Promo extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'disc_amount',
        'valid_until',
    ];

    protected $casts = [
        'disc_amount' => 'decimal:2',
        'valid_until' => 'datetime',
    ];


    

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }



    public function isValid(): bool
    {
        return Carbon::now()->lte($this->valid_until);
    }
}
