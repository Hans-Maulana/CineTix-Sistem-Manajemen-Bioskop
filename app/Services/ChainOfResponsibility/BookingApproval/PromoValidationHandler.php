<?php

namespace App\Services\ChainOfResponsibility\BookingApproval;

use App\Models\Promo;
use App\Services\ChainOfResponsibility\BookingApprovalHandler;

class PromoValidationHandler extends BookingApprovalHandler
{
    /**
     * Validasi: apakah promo code valid jika diberikan?
     */
    protected function approve(array $bookingData): array
    {
        $promoCode = $bookingData['promo_code'] ?? null;

        // Jika tidak ada promo code, skip handler ini
        if (empty($promoCode)) {
            return $this->approved();
        }

        // Cari promo code
        $promo = Promo::where('code', $promoCode)
            ->where('valid_until', '>=', now())
            ->first();

        if (!$promo) {
            return $this->reject(
                'Promo code "' . $promoCode . '" tidak valid atau sudah kadaluarsa.'
            );
        }

        // Validasi tambahan: cek max usage jika ada
        if ($promo->max_usage && $promo->usage_count >= $promo->max_usage) {
            return $this->reject(
                'Promo code "' . $promoCode . '" sudah mencapai batas penggunaan.'
            );
        }

        // Store promo ke booking data untuk digunakan di step selanjutnya
        $bookingData['promo_id'] = $promo->id;
        $bookingData['discount_amount'] = $promo->disc_amount;

        return [
            'approved' => true,
            'message' => 'Promo valid',
            'booking_data' => $bookingData,
        ];
    }
}
