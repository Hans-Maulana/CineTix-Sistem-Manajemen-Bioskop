<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\User;
use App\Models\Film;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $films = Film::all();

        $reviews = [
            [
                'user_id' => $users->first()->id ?? 1,
                'film_id' => $films->first()->id ?? 1,
                'rating' => 5,
                'comment' => 'Amazing movie! Highly recommended.',
            ],
            [
                'user_id' => $users->first()->id ?? 1,
                'film_id' => $films->skip(1)->first()->id ?? 2,
                'rating' => 4,
                'comment' => 'Great movie, but a bit long.',
            ],
            [
                'user_id' => $users->skip(1)->first()->id ?? 2,
                'film_id' => $films->skip(2)->first()->id ?? 3,
                'rating' => 5,
                'comment' => 'Best movie ever!',
            ],
        ];

        foreach ($reviews as $review) {
            Review::create($review);
        }
    }
}
