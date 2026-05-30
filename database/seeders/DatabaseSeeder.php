<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AdminSeeder::class,
            CustomerSeeder::class,
            GenreSeeder::class,
            TypeSeeder::class,
            PromoSeeder::class,
            FilmSeeder::class,
            StudioSeeder::class,
            ScheduleSeeder::class,
            SeatSeeder::class,
            BundlingSeeder::class,
            BookingSeeder::class,
            ReviewSeeder::class,
            GenreFilmSeeder::class,
        ]);
    }
}
