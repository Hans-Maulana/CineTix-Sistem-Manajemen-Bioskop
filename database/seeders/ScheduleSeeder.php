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

        // Buat rentang tanggal dari kemarin (untuk demo riwayat) sampai 14 hari ke depan
        $dates = [];
        for ($i = -1; $i <= 14; $i++) {
            $dates[] = $today->copy()->addDays($i)->toDateString();
        }

        $startTimes = ['10:00', '13:00', '15:30', '18:00', '20:30'];

        foreach ($films as $filmIndex => $film) {
            // Distribusikan ke beberapa studio secara bergiliran
            $studio = $studios->get($filmIndex % $studios->count());

            foreach ($dates as $dayIndex => $dateStr) {
                // Tiap film tayang 3-4 kali sehari per studio
                $dailyShowtimes = rand(2, 4);
                
                for ($s = 0; $s < $dailyShowtimes; $s++) {
                    $startTimeStr = $startTimes[($filmIndex + $dayIndex + $s) % count($startTimes)];
                    $startTime = Carbon::parse("$dateStr $startTimeStr");
                    $endTime = (clone $startTime)->addMinutes($film->duration);

                    // Tentukan status: jika jadwal sudah lewat waktu sekarang, set complete
                    $isPast = $startTime->isPast();
                    
                    $schedules[] = [
                        'film_id' => $film->id,
                        'studio_id' => $studio->id,
                        'schedule_date' => $dateStr,
                        'start_time' => $startTime->format('H:i:s'),
                        'end_time' => $endTime->format('H:i:s'),
                        'ticket_price' => 50000 + (($filmIndex % 3) * 10000) + (Carbon::parse($dateStr)->isWeekend() ? 15000 : 0),
                        'status' => $isPast ? 'complete' : 'on schedule',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                }
            }
        }

        foreach ($schedules as $schedule) {
            Schedule::create($schedule);
        }
    }
}

