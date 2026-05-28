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
        'description',
        'discount_type',
        'discount_value',
        'valid_from',
        'valid_until',
        'max_usage',
        'max_usage_per_customer',
        'usage_count',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'valid_from' => 'date',
        'valid_until' => 'date',
    ];

    /**
     * Relationship: Promo usage tracking
     */
    public function usages()
    {
        return $this->hasMany(PromoUsage::class);
    }

    /**
     * Relationship untuk bookings (compat lama)
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Check apakah promo masih valid (tanggal & max_usage)
     */
    public function isValid(): bool
    {
        $now = Carbon::now();
        
        // Check tanggal
        if ($now->lt($this->valid_from) || $now->gt($this->valid_until)) {
            return false;
        }

        // Check max_usage (jika ada)
        if ($this->max_usage && $this->usage_count >= $this->max_usage) {
            return false;
        }

        return true;
    }

    /**
     * Check apakah user sudah pake promo ini
     */
    public function hasBeenUsedByUser($userId): bool
    {
        return $this->usages()
            ->where('user_id', $userId)
            ->exists();
    }

    /**
     * Get usage count untuk user tertentu
     */
    public function getUserUsageCount($userId): int
    {
        $usage = $this->usages()
            ->where('user_id', $userId)
            ->first();

        return $usage ? $usage->usage_count : 0;
    }

    /**
     * Check apakah user masih bisa pakai promo ini
     */
    public function canBeUsedBy($userId): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        // Check max_usage_per_customer
        $userUsageCount = $this->getUserUsageCount($userId);
        return $userUsageCount < $this->max_usage_per_customer;
    }

    /**
     * Record promo usage untuk user
     */
    public function recordUsage($userId, $bookingId = null): PromoUsage
    {
        $usage = $this->usages()
            ->where('user_id', $userId)
            ->first();

        if ($usage) {
            // Update existing usage
            $usage->usage_count += 1;
            $usage->booking_id = $bookingId;
            $usage->save();
        } else {
            // Create new usage record
            $usage = PromoUsage::create([
                'promo_id' => $this->id,
                'user_id' => $userId,
                'booking_id' => $bookingId,
                'usage_count' => 1,
            ]);
        }

        // Update total usage count di promo
        $this->increment('usage_count');

        return $usage;
    }

    /**
     * Hitung discount amount
     */
    public function calculateDiscount($subtotal): float
    {
        if ($this->discount_type === 'percentage') {
            return ($subtotal * $this->discount_value) / 100;
        }
        
        // Fixed amount
        return min($this->discount_value, $subtotal);
    }

    public function discountLabel(): string
    {
        if ($this->discount_type === 'percentage') {
            return $this->discount_value . '%';
        }

        return 'Rp ' . number_format($this->discount_value, 0, ',', '.');
    }

    /**
     * Status promo untuk tampilan customer (guest atau member).
     */
    public function statusForUser(?int $userId): array
    {
        if (!$this->isValid()) {
            return ['label' => 'Tidak Berlaku', 'badge' => 'secondary'];
        }

        if (!$userId) {
            return ['label' => 'Login untuk Pakai', 'badge' => 'info'];
        }

        if (!$this->canBeUsedBy($userId)) {
            return ['label' => 'Sudah Digunakan', 'badge' => 'danger'];
        }

        return ['label' => 'Tersedia', 'badge' => 'success'];
    }

    public function remainingUsesFor(?int $userId): int
    {
        if (!$userId) {
            return $this->max_usage_per_customer;
        }

        return max(0, $this->max_usage_per_customer - $this->getUserUsageCount($userId));
    }
}
