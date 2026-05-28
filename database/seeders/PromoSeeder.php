<?php

namespace Database\Seeders;

use App\Models\Promo;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PromoSeeder extends Seeder
{
    public function run(): void
    {
        $promos = [
            [
                'code' => 'WELCOME2026',
                'description' => 'Diskon Rp20.000 untuk pengguna baru (1x per akun)',
                'discount_type' => 'fixed',
                'discount_value' => 20000,
                'valid_from' => Carbon::now()->startOfYear(),
                'valid_until' => Carbon::now()->addYear(),
                'max_usage' => null,
                'max_usage_per_customer' => 1,
                'usage_count' => 0,
            ],
            [
                'code' => 'PROMO10',
                'description' => 'Diskon Rp10.000',
                'discount_type' => 'fixed',
                'discount_value' => 10000,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addDays(30),
                'max_usage' => null,
                'max_usage_per_customer' => 1,
                'usage_count' => 0,
            ],
            [
                'code' => 'PROMO20',
                'description' => 'Diskon Rp20.000',
                'discount_type' => 'fixed',
                'discount_value' => 20000,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addDays(60),
                'max_usage' => null,
                'max_usage_per_customer' => 2,
                'usage_count' => 0,
            ],
        ];

        foreach ($promos as $promo) {
            Promo::updateOrCreate(
                ['code' => $promo['code']],
                $promo
            );
        }
    }
}
