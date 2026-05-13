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
        $interstellar = Film::where('title', 'Interstellar')->first();
        $studios = Studio::all();

        $schedules = [
            [
                'film_id' => $interstellar->id,
                'studio_id' => $studios->first()->id,
                'schedule_date' => Carbon::now()->addDays(1),
                'start_time' => '10:00',
                'end_time' => '12:50',
                'ticket_price' => 50000,
                'status' => 'on schedule',
            ],
            [
                'film_id' => $interstellar->id,
                'studio_id' => $studios->first()->id,
                'schedule_date' => Carbon::now()->addDays(1),
                'start_time' => '13:30',
                'end_time' => '16:20',
                'ticket_price' => 50000,
                'status' => 'on schedule',
            ],
            [
                'film_id' => $interstellar->id,
                'studio_id' => $studios->skip(1)->first()->id,
                'schedule_date' => Carbon::now()->addDays(2),
                'start_time' => '14:00',
                'end_time' => '16:50',
                'ticket_price' => 60000,
                'status' => 'on schedule',
            ],
            [
                'film_id' => $interstellar->id,
                'studio_id' => $studios->skip(2)->first()->id,
                'schedule_date' => Carbon::now(),
                'start_time' => '08:00',
                'end_time' => '10:50',
                'ticket_price' => 65000,
                'status' => 'complete',
            ],
        ];

        foreach ($schedules as $schedule) {
            Schedule::updateOrCreate(
                [
                    'film_id' => $schedule['film_id'],
                    'studio_id' => $schedule['studio_id'],
                    'start_time' => $schedule['start_time'],
                    'schedule_date' => $schedule['schedule_date']
                ],
                $schedule
            );
        }
    }
}

