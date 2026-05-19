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
                'title' => 'Avengers: Endgame',
                'synopsis' => 'After the devastating events of Infinity War, the universe is in ruins. With the help of remaining allies, the Avengers assemble once more in order to reverse Thanos\' actions and restore balance to the universe.',
                'duration' => 181,
                'rating' => 0,
                'actors' => 'Robert Downey Jr., Chris Evans, Mark Ruffalo',
                'director' => 'Anthony Russo, Joe Russo',
                'production' => 'Marvel Studios',
                'status' => 'now_playing',
                'classification' => 'PG-13',
                'cover' => 'avengers_endgame.webp',
                'release_date' => '2019-04-26',
            ],
            [
                'title' => 'Inception',
                'synopsis' => 'Dom Cobb is a skilled thief, the absolute best in the dangerous art of extraction, stealing valuable secrets from deep within the subconscious during the dream state.',
                'duration' => 148,
                'rating' => 0,
                'actors' => 'Leonardo DiCaprio, Joseph Gordon-Levitt, Elliot Page',
                'director' => 'Christopher Nolan',
                'production' => 'Warner Bros',
                'status' => 'now_playing',
                'classification' => 'PG-13',
                'cover' => 'inception.jpg',
                'release_date' => '2010-07-16',
            ],
            [
                'title' => 'The Dark Knight',
                'synopsis' => 'When the menace known as the Joker wreaks havoc and chaos on the people of Gotham, Batman must accept one of the greatest psychological and physical tests of his ability to fight injustice.',
                'duration' => 152,
                'rating' => 0,
                'actors' => 'Christian Bale, Heath Ledger, Aaron Eckhart',
                'director' => 'Christopher Nolan',
                'production' => 'Warner Bros',
                'status' => 'now_playing',
                'classification' => 'PG-13',
                'cover' => 'the_dark_knight.avif',
                'release_date' => '2008-07-18',
            ],
            [
                'title' => 'Interstellar',
                'synopsis' => 'In a future where Earth is becoming uninhabitable, farmer and ex-NASA pilot Joseph Cooper is tasked to pilot a spacecraft, along with a team of researchers, through a wormhole to find a new planet for humans.',
                'duration' => 169,
                'rating' => 0,
                'actors' => 'Matthew McConaughey, Anne Hathaway, Jessica Chastain',
                'director' => 'Christopher Nolan',
                'production' => 'Warner Bros',
                'status' => 'now_playing',
                'classification' => 'PG-13',
                'cover' => 'interstellar.jpg',
                'release_date' => '2014-11-07',
            ],
            [
                'title' => 'Spider-Man: No Way Home',
                'synopsis' => 'With Spider-Man\'s identity now revealed, Peter asks Doctor Strange for help. When a spell goes wrong, dangerous foes from other worlds start to appear, forcing Peter to discover what it truly means to be Spider-Man.',
                'duration' => 148,
                'rating' => 0,
                'actors' => 'Tom Holland, Zendaya, Benedict Cumberbatch',
                'director' => 'Jon Watts',
                'production' => 'Marvel Studios',
                'status' => 'now_playing',
                'classification' => 'PG-13',
                'cover' => 'spiderman_no_way_home.webp',
                'release_date' => '2021-12-17',
            ],
            [
                'title' => 'Toy Story 5',
                'synopsis' => 'Woody, Buzz, and the rest of the gang embark on a new adventure.',
                'duration' => 100,
                'rating' => 0,
                'actors' => 'Tom Hanks, Tim Allen',
                'director' => 'Andrew Stanton',
                'production' => 'Pixar Animation Studios',
                'status' => 'coming_soon',
                'classification' => 'SU',
                'cover' => 'toy_story_5.jpg',
                'release_date' => '2025-06-19',
            ],
            [
                'title' => 'Frozen 3',
                'synopsis' => 'Elsa and Anna embark on a new magical journey.',
                'duration' => 110,
                'rating' => 0,
                'actors' => 'Idina Menzel, Kristen Bell',
                'director' => 'Jennifer Lee',
                'production' => 'Walt Disney Animation Studios',
                'status' => 'coming_soon',
                'classification' => 'SU',
                'cover' => 'frozen_3.jpg',
                'release_date' => '2026-11-25',
            ],
            [
                'title' => 'Avatar 3',
                'synopsis' => 'Jake Sully and Neytiri explore new regions of Pandora.',
                'duration' => 190,
                'rating' => 0,
                'actors' => 'Sam Worthington, Zoe Saldana',
                'director' => 'James Cameron',
                'production' => 'Lightstorm Entertainment',
                'status' => 'coming_soon',
                'classification' => 'PG-13',
                'cover' => 'avatar_3.jpg',
                'release_date' => '2025-12-19',
            ],
            [
                'title' => 'Deadpool & Wolverine',
                'synopsis' => 'Deadpool and Wolverine team up in this multiverse adventure.',
                'duration' => 127,
                'rating' => 0,
                'actors' => 'Ryan Reynolds, Hugh Jackman',
                'director' => 'Shawn Levy',
                'production' => 'Marvel Studios',
                'status' => 'coming_soon',
                'classification' => 'R',
                'cover' => 'deadpool_wolverine.jpg',
                'release_date' => '2024-07-26',
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
