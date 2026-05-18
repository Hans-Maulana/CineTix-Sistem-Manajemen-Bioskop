<?php

namespace Database\Seeders;

use App\Models\Film;
use App\Models\Genre;
use Illuminate\Database\Seeder;

class GenreFilmSeeder extends Seeder
{
    public function run(): void
    {
        // The Avengers → Action
        $avengers = Film::where('title', 'The Avengers')->first();
        $action = Genre::where('genre_name', 'Action')->first();
        if ($avengers && $action) {
            $avengers->genres()->syncWithoutDetaching([$action->id]);
        }

        // Inception → Sci-Fi
        $inception = Film::where('title', 'Inception')->first();
        $scifi = Genre::where('genre_name', 'Sci-Fi')->first();
        if ($inception && $scifi) {
            $inception->genres()->syncWithoutDetaching([$scifi->id]);
        }

        // The Dark Knight → Action, Crime
        $darkKnight = Film::where('title', 'The Dark Knight')->first();
        $crime = Genre::where('genre_name', 'Action')->first();
        if ($darkKnight && $action && $crime) {
            $darkKnight->genres()->syncWithoutDetaching([$action->id, $crime->id]);
        }

        // Interstellar → Sci-Fi, Adventure
        $interstellar = Film::where('title', 'Interstellar')->first();
        $adventure = Genre::where('genre_name', 'Adventure')->first();
        if ($interstellar && $scifi && $adventure) {
            $interstellar->genres()->syncWithoutDetaching([$scifi->id, $adventure->id]);
        }

        // Toy Story → Animation, Family
        $toyStory = Film::where('title', 'Toy Story')->first();
        $animation = Genre::where('genre_name', 'Animation')->first();
        $family = Genre::where('genre_name', 'Adventure')->first();
        if ($toyStory && $animation && $family) {
            $toyStory->genres()->syncWithoutDetaching([$animation->id, $family->id]);
        }
    }
}
