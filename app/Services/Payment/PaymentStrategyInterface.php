<?php

namespace App\Services\Payment;

use App\Models\Booking;
use App\Models\Payment;

interface PaymentStrategyInterface
{
    /**
     * Initiate payment: buat record Payment baru dengan status pending.
     */
    public function initiate(Booking $booking): Payment;

    /**
     * Process payment: simulasi proses pembayaran.
     * Return true jika berhasil, false jika gagal.
     */
    public function process(Payment $payment): bool;

    /**
     * Get the method identifier.
     */
    public function getMethodName(): string;

    /**
     * Get data tambahan yang perlu ditampilkan di view (QR, VA number, dll).
     */
    public function getDisplayData(Payment $payment, Booking $booking): array;
}
