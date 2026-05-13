<?php

namespace Database\Seeders;

use App\Models\Seat;
use App\Models\Studio;
use Illuminate\Database\Seeder;

class SeatSeeder extends Seeder
{
    public function run(): void
    {
        $studios = Studio::all();
        $rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];

        foreach ($studios as $studio) {
            $seatsPerRow = $studio->capacity / count($rows);

            foreach ($rows as $row) {
                for ($seat = 1; $seat <= $seatsPerRow; $seat++) {
                    Seat::create([
                        'studio_id' => $studio->id,
                        'row_label' => $row,
                        'seat_number' => $seat,
                        'seat_code' => 'S' . $studio->id . '-' . $row . str_pad($seat, 2, '0', STR_PAD_LEFT),
                        'status' => 'available',
                    ]);
                }
            }
        }
    }
}

