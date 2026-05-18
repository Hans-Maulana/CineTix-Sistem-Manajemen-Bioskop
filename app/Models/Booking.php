<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'promo_id',
        'schedule_id',
        'booking_type',
        'total_amount',
        'status',
        'qr_redeem',
        'status_redeem',  
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];



    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function promo()
    {
        return $this->belongsTo(Promo::class);
    }

    public function ticketBookings()
    {
        return $this->hasMany(TicketBooking::class);
    }

    public function fnbBookings()
    {
        return $this->hasMany(FnbBooking::class);
    }

    public function bundlingBookings()
    {
        return $this->hasMany(BundlingBooking::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the latest payment for this booking.
     */
    public function latestPayment()
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }



    public function calculateTotal(): float
    {
        $ticketTotal = $this->ticketBookings->sum('price_at_sale');
        $fnbTotal    = $this->fnbBookings->sum(fn ($item) => $item->price_at_sale * $item->quantity);
        $subtotal    = $ticketTotal + $fnbTotal;

        if ($this->promo && $this->promo->isValid()) {
            $subtotal -= $this->promo->disc_amount;
        }

        return max(0, $subtotal);
    }

    public function applyPromo(string $promoCode): bool
    {
        $promo = Promo::where('code', $promoCode)->first();

        if ($promo && $promo->isValid()) {
            $this->promo_id = $promo->id;
            $this->save();
            return true;
        }

        return false;
    }

    public function generateQR(): string
    {
        $this->qr_redeem = Str::uuid();
        $this->save();

        return $this->qr_redeem;
    }

    public function redeemBooking(): bool
    {
        if ($this->status_redeem === 'unredeemed' && $this->status === 'confirmed') {
            $this->status_redeem = 'redeemed';
            $this->save();
            return true;
        }

        return false;
    }

    public function confirmBooking(): void
    {
        $this->status = 'confirmed';
        $this->save();
    }

    public function cancelBooking(): void
    {
        $this->status = 'cancelled';
        $this->save();
    }
}
