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
        $studioA = $studios->first();
        $studioB = $studios->skip(1)->first();
        $studioC = $studios->skip(2)->first();

        if ($films->isEmpty() || $studios->isEmpty()) {
            return;
        }

        $schedules = [];

        $today = Carbon::today();

        // Tanggal penayangan dinamis (hari ini, besok, lusa)
        $julyDates = [
            $today->toDateString(),
            $today->copy()->addDay()->toDateString(),
            $today->copy()->addDays(2)->toDateString(),
        ];

        // Tanggal penayangan dinamis minggu depan (7 hari, 8 hari, 9 hari lagi)
        $augustDates = [
            $today->copy()->addDays(7)->toDateString(),
            $today->copy()->addDays(8)->toDateString(),
            $today->copy()->addDays(9)->toDateString(),
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
                    'status' => $dayIndex === 0 ? 'complete' : 'on schedule',
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

