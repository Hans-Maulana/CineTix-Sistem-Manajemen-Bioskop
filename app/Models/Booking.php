<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    const REFUND_ADMIN_FEE_PERCENT = 10;
    const REFUND_MIN_HOURS_BEFORE = 2;

    protected $fillable = [
        'user_id',
        'guest_email',
        'access_token',
        'promo_id',
        'schedule_id',
        'booking_type',
        'total_amount',
        'status',
        'qr_redeem',
        'status_redeem',
        'refund_status',
        'refund_reason',
        'refund_amount',
        'refund_requested_at',
        'refund_processed_at',
        'refund_processed_by',
        'refund_rejection_reason',
    ];

    protected $casts = [
        'total_amount'         => 'decimal:2',
        'refund_amount'        => 'decimal:2',
        'refund_requested_at'  => 'datetime',
        'refund_processed_at'  => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isGuest(): bool
    {
        return $this->user_id === null && filled($this->guest_email);
    }

    public function customerEmail(): ?string
    {
        return $this->guest_email ?: ($this->user?->email ?: null);
    }

    public function customerName(): string
    {
        if ($this->isGuest()) {
            $email = $this->guest_email ?? '';
            $local = explode('@', $email)[0] ?? 'Guest';

            return 'Guest (' . ucfirst(str_replace(['.', '_', '-'], ' ', $local)) . ')';
        }

        return $this->user?->name ?? '-';
    }

    public function customerPhone(): ?string
    {
        if ($this->isGuest()) {
            return null;
        }

        return $this->user?->contact ?? null;
    }

    public function customerTypeLabel(): string
    {
        return $this->isGuest() ? 'Guest' : 'Member';
    }

    public function promo()
    {
        return $this->belongsTo(Promo::class);
    }

    public function refundProcessedBy()
    {
        return $this->belongsTo(User::class, 'refund_processed_by');
    }

    /**
     * Cek apakah booking ini bisa diajukan refund.
     * Syarat: status confirmed, belum ada refund sebelumnya,
     * film belum tayang (minimal REFUND_MIN_HOURS_BEFORE jam sebelum tayang),
     * dan bukan guest booking.
     */
    public function canRequestRefund(): bool
    {
        if ($this->user_id === null) {
            return false; // guest tidak bisa refund
        }

        if ($this->status !== 'confirmed') {
            return false;
        }

        if (!is_null($this->refund_status)) {
            return false; // sudah pernah mengajukan
        }

        $firstTicket = $this->ticketBookings->first();
        if (!$firstTicket || !$firstTicket->schedule) {
            return false;
        }

        $showDateTime = \Carbon\Carbon::parse(
            $firstTicket->schedule->schedule_date->format('Y-m-d')
            . ' ' .
            $firstTicket->schedule->start_time->format('H:i:s')
        );

        return now()->addHours(self::REFUND_MIN_HOURS_BEFORE)->lessThan($showDateTime);
    }

    /**
     * Potongan admin fee (dalam rupiah).
     */
    public function refundAdminFee(): float
    {
        return round($this->total_amount * self::REFUND_ADMIN_FEE_PERCENT / 100, 2);
    }

    /**
     * Jumlah yang dikembalikan ke customer (setelah potongan).
     */
    public function refundNetAmount(): float
    {
        return max(0, $this->total_amount - $this->refundAdminFee());
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
            $subtotal -= $this->promo->calculateDiscount($ticketTotal);
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
