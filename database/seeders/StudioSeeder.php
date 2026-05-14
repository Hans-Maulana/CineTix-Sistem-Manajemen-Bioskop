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

        // 1 = Seat, 0 = Aisle/Empty
        $layoutA = [
            [1, 1, 1, 0, 1, 1, 1, 1, 0, 1, 1, 1], // Row A
            [1, 1, 1, 0, 1, 1, 1, 1, 0, 1, 1, 1], // Row B
            [1, 1, 1, 0, 1, 1, 1, 1, 0, 1, 1, 1], // Row C
            [1, 1, 1, 0, 1, 1, 1, 1, 0, 1, 1, 1], // Row D
            [1, 1, 1, 0, 1, 1, 1, 1, 0, 1, 1, 1], // Row E
            [1, 1, 1, 0, 1, 1, 1, 1, 0, 1, 1, 1], // Row F
        ];

        $layoutB = [
            [1, 1, 0, 1, 1, 1, 1, 0, 1, 1],
            [1, 1, 0, 1, 1, 1, 1, 0, 1, 1],
            [1, 1, 0, 1, 1, 1, 1, 0, 1, 1],
            [1, 1, 0, 1, 1, 1, 1, 0, 1, 1],
            [1, 1, 0, 1, 1, 1, 1, 0, 1, 1],
        ];

        $studios = [
            [
                'type_id' => $types->firstWhere('name', '2D')->id ?? 1,
                'name' => 'Studio A',
                'capacity' => 45,
                'seat_layout' => $layoutA,
                'status' => 'active',
            ],
            [
                'type_id' => $types->firstWhere('name', '3D')->id ?? 2,
                'name' => 'Studio B',
                'capacity' => 40,
                'seat_layout' => $layoutB,
                'status' => 'active',
            ],
            [
                'type_id' => $types->firstWhere('name', 'IMAX')->id ?? 3,
                'name' => 'Studio C',
                'capacity' => 40,
                'seat_layout' => $layoutB,
                'status' => 'active',
            ],
        ];

        foreach ($studios as $studio) {
            Studio::updateOrCreate(
                ['name' => $studio['name']],
                $studio
            );
        }
    }
}
