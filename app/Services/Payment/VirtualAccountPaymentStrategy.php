<?php

namespace App\Services\Payment;

use App\Models\Booking;
use App\Models\Payment;

class VirtualAccountPaymentStrategy implements PaymentStrategyInterface
{
    /**
     * Prefix VA CineTix
     */
    private const VA_PREFIX = '7708';

    /**
     * Countdown timer: 15 menit (900 detik)
     */
    private const COUNTDOWN_SECONDS = 900;

    public function initiate(Booking $booking): Payment
    {
        $vaNumber = $this->generateVANumber($booking->user_id);

        return Payment::create([
            'booking_id' => $booking->id,
            'amount' => $booking->total_amount,
            'method' => $this->getMethodName(),
            'status' => 'pending',
            'va_number' => $vaNumber,
            'countdown_seconds' => self::COUNTDOWN_SECONDS,
        ]);
    }

    public function process(Payment $payment): bool
    {
        // Simulasi: selalu berhasil
        $payment->update([
            'status' => 'success',
            'paid_at' => now(),
        ]);

        // Update booking status
        $payment->booking->confirmBooking();

        return true;
    }

    public function getMethodName(): string
    {
        return 'virtual_account';
    }

    public function getDisplayData(Payment $payment, Booking $booking): array
    {
        return [
            'va_number' => $payment->va_number,
            'bank_name' => 'CineTix Virtual Account',
            'countdown_seconds' => self::COUNTDOWN_SECONDS,
            'method_label' => 'Virtual Account',
            'instructions' => [
                'Buka aplikasi mobile banking atau ATM',
                'Pilih menu "Transfer" atau "Virtual Account"',
                'Masukkan nomor Virtual Account: ' . $payment->va_number,
                'Masukkan jumlah pembayaran sesuai yang tertera',
                'Konfirmasi dan selesaikan transfer',
                'Klik tombol "Selesaikan Pembayaran" setelah berhasil',
            ],
        ];
    }

    /**
     * Generate VA number: prefix 7708 + user ID (padded to 8 digits)
     */
    private function generateVANumber(int $userId): string
    {
        return self::VA_PREFIX . str_pad($userId, 8, '0', STR_PAD_LEFT);
    }
}
