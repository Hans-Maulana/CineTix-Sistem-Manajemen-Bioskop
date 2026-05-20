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

        foreach ($studios as $studio) {
            // Hapus seat lama agar tidak duplikat saat re-seed
            $studio->seats()->delete();

            if ($studio->seat_layout) {
                foreach ($studio->seat_layout as $rowIndex => $row) {
                    $rowLabel = chr(65 + $rowIndex); // 0 -> A, 1 -> B, ...
                    $seatCounter = 1;
                    foreach ($row as $colIndex => $isSeat) {
                        if ($isSeat == 1) {
                            $seatNumber = $seatCounter;
                            Seat::create([
                                'studio_id' => $studio->id,
                                'row_label' => $rowLabel,
                                'seat_number' => $seatNumber,
                                'seat_code' => $rowLabel . $seatNumber,
                                'status' => 'available',
                            ]);
                            $seatCounter++;
                        }
                    }
                }
            } else {
                // Fallback jika tidak ada layout (grid standar)
                $rows = ['A', 'B', 'C', 'D', 'E'];
                $seatsPerRow = floor($studio->capacity / count($rows));

                foreach ($rows as $row) {
                    for ($seat = 1; $seat <= $seatsPerRow; $seat++) {
                        Seat::create([
                            'studio_id' => $studio->id,
                            'row_label' => $row,
                            'seat_number' => $seat,
                            'seat_code' => $row . $seat,
                            'status' => 'available',
                        ]);
                    }
                }
            }
        }
    }
}
