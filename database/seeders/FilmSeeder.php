<?php

namespace Database\Seeders;

use App\Models\Film;
use Illuminate\Database\Seeder;

class FilmSeeder extends Seeder
{
    public function run(): void
    {
        $films = [
            [
                'title' => 'The Avengers',
                'synopsis' => 'Nick Fury, director of SHIELD, assembles a team of superheroes to save the world from the god Loki and his alien army.',
                'duration' => 143,
                'rating' => 8.0,
                'actors' => 'Robert Downey Jr., Chris Evans, Chris Hemsworth',
                'director' => 'Joss Whedon',
                'production' => 'Marvel Studios',
                'status' => 'ended',
                'classification' => 'PG-13',
                'cover' => 'avengers.jpg',
                'release_date' => '2012-05-04',
            ],
            [
                'title' => 'Inception',
                'synopsis' => 'Dom Cobb is a skilled thief, the absolute best in the dangerous art of extraction, stealing valuable secrets from deep within the subconscious during the dream state.',
                'duration' => 148,
                'rating' => 8.8,
                'actors' => 'Leonardo DiCaprio, Ellen Page, Joseph Gordon-Levitt',
                'director' => 'Christopher Nolan',
                'production' => 'Warner Bros',
                'status' => 'ended',
                'classification' => 'PG-13',
                'cover' => 'inception.jpg',
                'release_date' => '2010-07-16',
            ],
            [
                'title' => 'The Dark Knight',
                'synopsis' => 'When the menace known as the Joker wreaks havoc and chaos on the people of Gotham, Batman must accept one of the greatest psychological and physical tests of his ability to fight injustice.',
                'duration' => 152,
                'rating' => 9.0,
                'actors' => 'Christian Bale, Heath Ledger, Aaron Eckhart',
                'director' => 'Christopher Nolan',
                'production' => 'Warner Bros',
                'status' => 'ended',
                'classification' => 'PG-13',
                'cover' => 'dark_knight.jpg',
                'release_date' => '2008-07-18',
            ],
            [
                'title' => 'Interstellar',
                'synopsis' => 'In a future where Earth is becoming uninhabitable, farmer and ex-NASA pilot Joseph Cooper is tasked to pilot a spacecraft, along with a team of researchers, through a wormhole to find a new planet for humans.',
                'duration' => 169,
                'rating' => 8.6,
                'actors' => 'Matthew McConaughey, Anne Hathaway, Jessica Chastain',
                'director' => 'Christopher Nolan',
                'production' => 'Warner Bros',
                'status' => 'now_playing',
                'classification' => 'PG-13',
                'cover' => 'interstellar.jpg',
                'release_date' => '2014-11-07',
            ],
            [
                'title' => 'Toy Story',
                'synopsis' => 'A cowboy doll is profoundly threatened and jealous when a new spaceman figure supplants him as top toy in a boy\'s room.',
                'duration' => 81,
                'rating' => 8.3,
                'actors' => 'Tom Hanks, Tim Allen',
                'director' => 'John Lasseter',
                'production' => 'Pixar',
                'status' => 'coming_soon',
                'classification' => 'G',
                'cover' => 'toy_story.jpg',
                'release_date' => '1995-11-22',
            ],
        ];

        foreach ($films as $filmData) {
            Film::updateOrCreate(
                ['title' => $filmData['title']],
                $filmData
            );
        }
    }
}
