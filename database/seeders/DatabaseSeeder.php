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
            TypeSeeder::class,
            GenreSeeder::class,
            FilmSeeder::class,
            StudioSeeder::class,
            ScheduleSeeder::class,
            SeatSeeder::class,
            PromoSeeder::class,
            BundlingSeeder::class,
            ReviewSeeder::class,
            GenreFilmSeeder::class,
        ]);
    }
}
