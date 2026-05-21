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
        $avengers = Film::where('title', 'Avengers: Endgame')->first();
        $inception = Film::where('title', 'Inception')->first();
        $theDarkKnight = Film::where('title', 'The Dark Knight')->first();
        $interstellar = Film::where('title', 'Interstellar')->first();
        $spiderman = Film::where('title', 'Spider-Man: No Way Home')->first();

        $studios = Studio::all();
        $studioA = $studios->first();
        $studioB = $studios->skip(1)->first();
        $studioC = $studios->skip(2)->first();

        $schedules = [
            // Studio A Schedules
            [
                'film_id' => $avengers->id,
                'studio_id' => $studioA->id,
                'schedule_date' => Carbon::now()->subDays(2),
                'start_time' => '13:00',
                'end_time' => '16:01',
                'ticket_price' => 50000,
                'status' => 'complete',
            ],
            [
                'film_id' => $avengers->id,
                'studio_id' => $studioA->id,
                'schedule_date' => Carbon::now(),
                'start_time' => '18:00',
                'end_time' => '21:01',
                'ticket_price' => 55000,
                'status' => 'on schedule',
            ],

            // Studio B Schedules
            [
                'film_id' => $inception->id,
                'studio_id' => $studioB->id,
                'schedule_date' => Carbon::now()->subDays(1),
                'start_time' => '14:00',
                'end_time' => '16:28',
                'ticket_price' => 50000,
                'status' => 'complete',
            ],
            [
                'film_id' => $inception->id,
                'studio_id' => $studioB->id,
                'schedule_date' => Carbon::now()->addDays(1),
                'start_time' => '13:00',
                'end_time' => '15:28',
                'ticket_price' => 50000,
                'status' => 'on schedule',
            ],

            // Studio C Schedules
            [
                'film_id' => $interstellar->id,
                'studio_id' => $studioC->id,
                'schedule_date' => Carbon::now()->subDays(3),
                'start_time' => '10:00',
                'end_time' => '12:49',
                'ticket_price' => 60000,
                'status' => 'complete',
            ],
            [
                'film_id' => $interstellar->id,
                'studio_id' => $studioC->id,
                'schedule_date' => Carbon::now(),
                'start_time' => '14:00',
                'end_time' => '16:49',
                'ticket_price' => 60000,
                'status' => 'on schedule',
            ],
            [
                'film_id' => $spiderman->id,
                'studio_id' => $studioC->id,
                'schedule_date' => Carbon::now(),
                'start_time' => '20:00',
                'end_time' => '22:28',
                'ticket_price' => 60000,
                'status' => 'on schedule',
            ],
        ];

        foreach ($schedules as $schedule) {
            Schedule::updateOrCreate(
                [
                    'studio_id' => $schedule['studio_id'],
                    'schedule_date' => $schedule['schedule_date']->format('Y-m-d'),
                    'start_time' => $schedule['start_time']
                ],
                $schedule
            );
        }
    }
}

