<?php

namespace Database\Seeders;

use App\Models\Schedule;
use App\Models\Film;
use App\Models\Studio;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus jadwal tayang lama agar tidak bentrok
        Schedule::query()->delete();

        $films = Film::all();
        $studios = Studio::all();

        if ($films->isEmpty() || $studios->isEmpty()) {
            return;
        }

        $schedules = [];

        // Tanggal penayangan di bulan Juli 2026 dan Agustus 2026
        $julyDates = [
            '2026-07-05',
            '2026-07-15',
            '2026-07-25',
        ];

        $augustDates = [
            '2026-08-05',
            '2026-08-15',
            '2026-08-25',
        ];

        $startTimes = ['10:00', '13:30', '16:30', '19:30'];

        foreach ($films as $filmIndex => $film) {
            // Distribusikan studio berdasarkan index film
            $studio = $studios->get($filmIndex % $studios->count());

            // 3 jadwal tayang di bulan Juli
            foreach ($julyDates as $dayIndex => $dateStr) {
                $startTimeStr = $startTimes[($filmIndex + $dayIndex) % count($startTimes)];
                $startTime = Carbon::parse("$dateStr $startTimeStr");
                $endTime = (clone $startTime)->addMinutes($film->duration);

                $schedules[] = [
                    'film_id' => $film->id,
                    'studio_id' => $studio->id,
                    'schedule_date' => $dateStr,
                    'start_time' => $startTime->format('H:i:s'),
                    'end_time' => $endTime->format('H:i:s'),
                    'ticket_price' => 50000 + (($filmIndex % 3) * 10000), // bervariasi 50rb, 60rb, 70rb
                    'status' => 'on schedule',
                ];
            }

            // 3 jadwal tayang di bulan Agustus
            foreach ($augustDates as $dayIndex => $dateStr) {
                $startTimeStr = $startTimes[($filmIndex + $dayIndex + 2) % count($startTimes)];
                $startTime = Carbon::parse("$dateStr $startTimeStr");
                $endTime = (clone $startTime)->addMinutes($film->duration);

                $schedules[] = [
                    'film_id' => $film->id,
                    'studio_id' => $studio->id,
                    'schedule_date' => $dateStr,
                    'start_time' => $startTime->format('H:i:s'),
                    'end_time' => $endTime->format('H:i:s'),
                    'ticket_price' => 55000 + (($filmIndex % 3) * 10000), // bervariasi 55rb, 65rb, 75rb
                    'status' => 'on schedule',
                ];
            }
        }

        foreach ($schedules as $schedule) {
            Schedule::create($schedule);
        }
    }
}

