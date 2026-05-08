<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bundling extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];




    public function fnbs()
    {
        return $this->belongsToMany(Fnb::class, 'bundling_fnb', 'bundling_id', 'fnb_id');
    }

    public function bundlingBookings()
    {
        return $this->hasMany(BundlingBooking::class);
    }
}
