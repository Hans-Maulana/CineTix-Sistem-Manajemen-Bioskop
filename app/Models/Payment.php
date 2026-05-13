<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'amount',
        'method',       // 'cash' | 'transfer' | 'ewallet' | 'qris' | 'virtual_account'
        'status',       // 'pending' | 'success' | 'failed'
        'va_number',
        'countdown_seconds',
        'paid_at',
    ];

    protected $casts = [
        'amount'            => 'decimal:2',
        'paid_at'           => 'datetime',
        'countdown_seconds' => 'integer',
    ];

    // -------------------------
    // Relationships
    // -------------------------

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    // -------------------------
    // Helper Methods
    // -------------------------

    /**
     * Check apakah payment sudah expired (countdown habis).
     */
    public function isExpired(): bool
    {
        if (!$this->countdown_seconds) {
            return false;
        }

        $expiresAt = $this->created_at->addSeconds($this->countdown_seconds);
        return now()->greaterThan($expiresAt);
    }

    /**
     * Get sisa waktu dalam detik.
     */
    public function getRemainingSecondsAttribute(): int
    {
        if (!$this->countdown_seconds) {
            return 0;
        }

        $expiresAt = $this->created_at->addSeconds($this->countdown_seconds);
        $remaining = now()->diffInSeconds($expiresAt, false);

        return max(0, (int) $remaining);
    }

    /**
     * Mark payment sebagai success.
     */
    public function markAsSuccess(): bool
    {
        $this->status = 'success';
        $this->paid_at = now();
        $saved = $this->save();

        if ($saved) {
            $this->booking->confirmBooking();
        }

        return $saved;
    }

    /**
     * Mark payment sebagai failed.
     */
    public function markAsFailed(): bool
    {
        $this->status = 'failed';
        return $this->save();
    }

    /**
     * Get human-readable method label.
     */
    public function getMethodLabelAttribute(): string
    {
        return match ($this->method) {
            'qris' => 'QRIS',
            'virtual_account' => 'Virtual Account',
            'cash' => 'Tunai',
            'transfer' => 'Transfer Bank',
            'ewallet' => 'E-Wallet',
            default => ucfirst($this->method),
        };
    }

    /**
     * Get human-readable status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Pembayaran',
            'success' => 'Berhasil',
            'failed' => 'Gagal',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get status badge CSS class.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'bg-warning',
            'success' => 'bg-success',
            'failed' => 'bg-danger',
            default => 'bg-secondary',
        };
    }
}
