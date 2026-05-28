<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PromoUsage extends Model
{
    use HasFactory;

    protected $table = 'promo_usages';

    protected $fillable = [
        'promo_id',
        'user_id',
        'booking_id',
        'usage_count',
    ];

    public function promo()
    {
        return $this->belongsTo(Promo::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
