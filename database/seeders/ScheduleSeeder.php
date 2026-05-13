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

        $schedules = [
            [
                'film_id' => $films->first()->id ?? 1,
                'studio_id' => $studios->first()->id ?? 1,
                'schedule_date' => Carbon::now()->addDays(1),
                'start_time' => '10:00',
                'end_time' => '12:00',
                'ticket_price' => 50000,
            ],
            [
                'film_id' => $films->first()->id ?? 1,
                'studio_id' => $studios->first()->id ?? 1,
                'schedule_date' => Carbon::now()->addDays(1),
                'start_time' => '13:00',
                'end_time' => '15:00',
                'ticket_price' => 50000,
            ],
            [
                'film_id' => $films->skip(1)->first()->id ?? 2,
                'studio_id' => $studios->skip(1)->first()->id ?? 2,
                'schedule_date' => Carbon::now()->addDays(2),
                'start_time' => '14:00',
                'end_time' => '16:30',
                'ticket_price' => 60000,
            ],
            [
                'film_id' => $films->skip(2)->first()->id ?? 3,
                'studio_id' => $studios->skip(2)->first()->id ?? 3,
                'schedule_date' => Carbon::now()->addDays(2),
                'start_time' => '19:00',
                'end_time' => '21:30',
                'ticket_price' => 65000,
            ],
        ];

        foreach ($schedules as $schedule) {
            Schedule::create($schedule);
        }
    }
}

