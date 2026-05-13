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
                'description' => 'Earth\'s mightiest heroes must come together and learn to fight as a team.',
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
                'description' => 'A skilled thief who steals corporate secrets through dream-sharing technology.',
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
                'description' => 'When the menace known as The Joker wreaks havoc on Gotham.',
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
                'description' => 'A team of explorers travel through a wormhole in space.',
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
                'description' => 'A cowboy doll is accidentally displaced and must team up with Buzz.',
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
            Film::firstOrCreate(
                ['title' => $filmData['title']],
                $filmData
            );
        }
    }
}
