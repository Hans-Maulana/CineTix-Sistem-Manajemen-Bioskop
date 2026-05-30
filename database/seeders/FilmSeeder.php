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
                'status' => 'now_playing',
                'classification' => 'PG-13',
                'cover' => 'avengers_endgame.webp',
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
                'status' => 'now_playing',
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
                'status' => 'now_playing',
                'classification' => 'PG-13',
                'cover' => 'the_dark_knight.avif',
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
                'status' => 'now_playing',
                'classification' => 'G',
                'cover' => 'toy_story_5.jpg',
                'release_date' => '1995-11-22',
            ],
            [
                'title' => 'Avatar 3',
                'synopsis' => 'Jake Sully and Neytiri face a new threat on Pandora from a fire-based Na\'vi clan known as the Ash People.',
                'duration' => 160,
                'rating' => 0.0,
                'actors' => 'Sam Worthington, Zoe Saldana, Sigourney Weaver',
                'director' => 'James Cameron',
                'production' => '20th Century Studios',
                'status' => 'coming_soon',
                'classification' => 'PG-13',
                'cover' => 'avatar_3.jpg',
                'release_date' => '2025-12-19',
            ],
            [
                'title' => 'Deadpool & Wolverine',
                'synopsis' => 'Wolverine is recovering from his injuries when he crosses paths with the loudmouth Deadpool. They team up to defeat a common enemy.',
                'duration' => 127,
                'rating' => 0.0,
                'actors' => 'Ryan Reynolds, Hugh Jackman, Emma Corrin',
                'director' => 'Shawn Levy',
                'production' => 'Marvel Studios',
                'status' => 'coming_soon',
                'classification' => 'R',
                'cover' => 'deadpool_wolverine.jpg',
                'release_date' => '2024-07-26',
            ],
            [
                'title' => 'Frozen 3',
                'synopsis' => 'The adventures of Elsa, Anna, Kristoff, Olaf and Sven continue in the third installment of the Frozen franchise.',
                'duration' => 100,
                'rating' => 0.0,
                'actors' => 'Kristen Bell, Idina Menzel, Josh Gad',
                'director' => 'Jennifer Lee',
                'production' => 'Walt Disney Pictures',
                'status' => 'coming_soon',
                'classification' => 'G',
                'cover' => 'frozen_3.jpg',
                'release_date' => '2026-11-25',
            ],
            [
                'title' => 'Spider-Man: No Way Home',
                'synopsis' => 'With Spider-Man\'s identity now revealed, Peter asks Doctor Strange for help. When a spell goes wrong, dangerous foes from other worlds start to appear.',
                'duration' => 148,
                'rating' => 8.2,
                'actors' => 'Tom Holland, Zendaya, Benedict Cumberbatch',
                'director' => 'Jon Watts',
                'production' => 'Columbia Pictures',
                'status' => 'coming_soon',
                'classification' => 'PG-13',
                'cover' => 'spiderman_no_way_home.webp',
                'release_date' => '2021-12-17',
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
