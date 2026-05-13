<?php

namespace Database\Seeders;

use App\Models\Studio;
use App\Models\Type;
use Illuminate\Database\Seeder;

class StudioSeeder extends Seeder
{
    public function run(): void
    {
        $types = Type::all();

        $studios = [
            [
                'type_id' => $types->firstWhere('name', '2D')->id ?? 1,
                'name' => 'Studio A',
                'capacity' => 100,
                'status' => 'active',
            ],
            [
                'type_id' => $types->firstWhere('name', '2D')->id ?? 1,
                'name' => 'Studio B',
                'capacity' => 120,
                'status' => 'active',
            ],
            [
                'type_id' => $types->firstWhere('name', '3D')->id ?? 2,
                'name' => 'Studio C',
                'capacity' => 80,
                'status' => 'active',
            ],
            [
                'type_id' => $types->firstWhere('name', '4D Experience')->id ?? 3,
                'name' => 'Studio D',
                'capacity' => 200,
                'status' => 'active',
            ],
        ];

        foreach ($studios as $studio) {
            Studio::firstOrCreate(
                ['name' => $studio['name']],
                $studio
            );
        }
    }
}

