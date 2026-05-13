<?php

namespace App\Services\Payment;

use App\Models\Booking;
use App\Models\Payment;

class QrisPaymentStrategy implements PaymentStrategyInterface
{
    /**
     * Countdown timer: 5 menit (300 detik)
     */
    private const COUNTDOWN_SECONDS = 300;

    public function initiate(Booking $booking): Payment
    {
        return Payment::create([
            'booking_id' => $booking->id,
            'amount' => $booking->total_amount,
            'method' => $this->getMethodName(),
            'status' => 'pending',
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
        return 'qris';
    }

    public function getDisplayData(Payment $payment, Booking $booking): array
    {
        // Generate QRIS data string (simulasi)
        $qrisData = 'CINETIX-QRIS-' . str_pad($booking->id, 8, '0', STR_PAD_LEFT) . '-' . $payment->id;

        return [
            'qr_url' => 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($qrisData),
            'qr_data' => $qrisData,
            'countdown_seconds' => self::COUNTDOWN_SECONDS,
            'method_label' => 'QRIS',
            'instructions' => [
                'Buka aplikasi e-wallet atau mobile banking Anda',
                'Pilih menu "Scan QR" atau "QRIS"',
                'Scan kode QR yang ditampilkan',
                'Konfirmasi pembayaran di aplikasi Anda',
                'Klik tombol "Selesaikan Pembayaran" setelah berhasil',
            ],
        ];
    }
}
