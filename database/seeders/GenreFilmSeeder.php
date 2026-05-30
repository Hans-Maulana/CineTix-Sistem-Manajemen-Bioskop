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

        // Genres variables
        $action = Genre::where('genre_name', 'Action')->first();
        $adventure = Genre::where('genre_name', 'Adventure')->first();
        $scifi = Genre::where('genre_name', 'Sci-Fi')->first();
        $comedy = Genre::where('genre_name', 'Comedy')->first();

        // Avatar 3 → Sci-Fi, Adventure, Action
        $avatar3 = Film::where('title', 'Avatar 3')->first();
        if ($avatar3) {
            $genreIds = array_filter([$scifi?->id, $adventure?->id, $action?->id]);
            $avatar3->genres()->syncWithoutDetaching($genreIds);
        }

        // Deadpool & Wolverine → Action, Comedy, Sci-Fi
        $deadpool = Film::where('title', 'Deadpool & Wolverine')->first();
        if ($deadpool) {
            $genreIds = array_filter([$action?->id, $comedy?->id, $scifi?->id]);
            $deadpool->genres()->syncWithoutDetaching($genreIds);
        }

        // Frozen 3 → Animation, Adventure, Comedy
        $frozen3 = Film::where('title', 'Frozen 3')->first();
        if ($frozen3) {
            $genreIds = array_filter([$animation?->id, $adventure?->id, $comedy?->id]);
            $frozen3->genres()->syncWithoutDetaching($genreIds);
        }

        // Spider-Man: No Way Home → Action, Adventure, Sci-Fi
        $spiderman = Film::where('title', 'Spider-Man: No Way Home')->first();
        if ($spiderman) {
            $genreIds = array_filter([$action?->id, $adventure?->id, $scifi?->id]);
            $spiderman->genres()->syncWithoutDetaching($genreIds);
        }
    }
}
