<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
    public function run(): void
    {
        $genres = [
            ['genre_name' => 'Action'],
            ['genre_name' => 'Adventure'],
            ['genre_name' => 'Animation'],
            ['genre_name' => 'Comedy'],
            ['genre_name' => 'Crime'],
            ['genre_name' => 'Documentary'],
            ['genre_name' => 'Drama'],
            ['genre_name' => 'Family'],
            ['genre_name' => 'Fantasy'],
            ['genre_name' => 'Horror'],
            ['genre_name' => 'Mystery'],
            ['genre_name' => 'Romance'],
            ['genre_name' => 'Sci-Fi'],
            ['genre_name' => 'Thriller'],
            ['genre_name' => 'Western'],
        ];

        foreach ($genres as $genre) {
            Genre::firstOrCreate(['genre_name' => $genre['genre_name']]);
        }
    }
}
