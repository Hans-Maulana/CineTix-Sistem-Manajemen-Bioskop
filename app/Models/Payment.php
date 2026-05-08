<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'method',       // 'credit_card' | 'e_wallet' | 'bank_transfer' | 'cash'
        'amount_paid',
        'paid_status',  // 'pending' | 'paid' | 'refunded' | 'failed'
        'paid_date',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'paid_date'   => 'datetime',
    ];

    // -------------------------
    // Relationships
    // -------------------------

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }


    // Helper Methods (Strategy Pattern: setiap method bisa dikembangkan)
    

    public function processPayment(): bool
    {
        $this->paid_status = 'paid';
        $this->paid_date   = now();
        $saved = $this->save();

        if ($saved) {
            $this->booking->confirmBooking();
        }

        return $saved;
    }

    public function refundPayment(): bool
    {
        if ($this->paid_status === 'paid') {
            $this->paid_status = 'refunded';
            $this->save();
            $this->booking->cancelBooking();
            return true;
        }

        return false;
    }

    public function cancelPayment(): bool
    {
        if ($this->paid_status === 'pending') {
            $this->paid_status = 'failed';
            $this->save();
            $this->booking->cancelBooking();
            return true;
        }

        return false;
    }
}
