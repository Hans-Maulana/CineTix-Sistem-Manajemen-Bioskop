<?php

namespace App\Services\ChainOfResponsibility\BookingApproval;

use App\Models\Promo;
use App\Models\Schedule;
use App\Services\ChainOfResponsibility\BookingApprovalHandler;
use Illuminate\Support\Facades\Auth;

class PromoValidationHandler extends BookingApprovalHandler
{
    protected function approve(array $bookingData): array
    {
        $promoCode = trim($bookingData['promo_code'] ?? '');

        if ($promoCode === '') {
            return [
                'approved' => true,
                'message' => 'OK',
                'booking_data' => $bookingData,
            ];
        }

        if (!Auth::check()) {
            return $this->reject(
                'Silakan login untuk menggunakan kode promo. Pengguna baru mendapat diskon Rp 20.000 dengan kode WELCOME2026.'
            );
        }

        $schedule = Schedule::find($bookingData['schedule_id'] ?? null);
        if ($schedule) {
            $seatCount = count($bookingData['seat_ids'] ?? []);
            $bookingData['subtotal'] = $schedule->ticket_price * $seatCount;
        }

        $promo = Promo::where('code', strtoupper($promoCode))->first();

        if (!$promo) {
            return $this->reject('Promo code "' . $promoCode . '" tidak ditemukan.');
        }

        if (!$promo->isValid()) {
            return $this->reject('Promo code "' . $promoCode . '" tidak valid atau sudah expired.');
        }

        if (!$promo->canBeUsedBy(Auth::id())) {
            return $this->reject('Anda sudah mencapai batas penggunaan kode promo ini.');
        }

        $discountAmount = $promo->calculateDiscount(
            $bookingData['subtotal'] ?? ($bookingData['total_amount'] ?? 0)
        );

        $bookingData['promo_id'] = $promo->id;
        $bookingData['discount_amount'] = $discountAmount;

        return [
            'approved' => true,
            'message' => 'Promo valid',
            'booking_data' => $bookingData,
        ];
    }
}
