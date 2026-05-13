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
                'code' => 'PROMO10',
                'description' => 'Diskon Rp10.000',
                'discount_type' => 'fixed',
                'discount_value' => 10000,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addDays(30),
            ],
            [
                'code' => 'PROMO20',
                'description' => 'Diskon Rp20.000',
                'discount_type' => 'fixed',
                'discount_value' => 20000,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addDays(60),
            ],
            [
                'code' => 'WEEKEND50',
                'description' => 'Diskon Rp50.000 untuk weekend',
                'discount_type' => 'fixed',
                'discount_value' => 50000,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addDays(90),
            ],
            [
                'code' => 'STUDENT15',
                'description' => 'Diskon Rp15.000 untuk pelajar',
                'discount_type' => 'fixed',
                'discount_value' => 15000,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addDays(45),
            ],
        ];

        foreach ($promos as $promo) {
            Promo::firstOrCreate(['code' => $promo['code']], $promo);
        }


        foreach ($promos as $promo) {
            Promo::firstOrCreate(
                ['code' => $promo['code']],
                $promo
            );
        }
    }
}
