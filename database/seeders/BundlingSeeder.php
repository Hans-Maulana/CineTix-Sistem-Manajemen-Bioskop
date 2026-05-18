<?php

namespace Database\Seeders;

use App\Models\Bundling;
use Illuminate\Database\Seeder;

class BundlingSeeder extends Seeder
{
    public function run(): void
    {
        $bundles = [
            [
                'name' => 'Basic Bundle',
                'price' => 75000,
            ],
            [
                'name' => 'Premium Bundle',
                'price' => 120000,
            ],
            [
                'name' => 'Family Bundle',
                'price' => 250000,
            ],
            [
                'name' => 'Group Bundle',
                'price' => 350000,
            ],
        ];

        foreach ($bundles as $bundle) {
            Bundling::firstOrCreate(
                ['name' => $bundle['name']],
                $bundle
            );
        }
    }
}
