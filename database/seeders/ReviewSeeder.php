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
        $customer = User::where('email', 'customer@bioskop.com')->first();
        if (!$customer) {
            $this->command->error('User customer@bioskop.com tidak ditemukan. Ulasan tidak dapat di-seed.');
            return;
        }

        $avengers = Film::where('title', 'Avengers: Endgame')->first();
        $inception = Film::where('title', 'Inception')->first();
        $interstellar = Film::where('title', 'Interstellar')->first();

        if (!$avengers || !$inception || !$interstellar) {
            $this->command->error('Film Avengers, Inception atau Interstellar tidak ditemukan. Ulasan tidak dapat di-seed.');
            return;
        }

        // Seeding other dummy reviewers
        $budi = User::firstOrCreate(
            ['email' => 'budi@bioskop.com'],
            [
                'role_id' => $customer->role_id,
                'name' => 'Budi',
                'password' => bcrypt('password'),
                'contact' => '083333333333'
            ]
        );

        $ani = User::firstOrCreate(
            ['email' => 'ani@bioskop.com'],
            [
                'role_id' => $customer->role_id,
                'name' => 'Ani',
                'password' => bcrypt('password'),
                'contact' => '084444444444'
            ]
        );

        $citra = User::firstOrCreate(
            ['email' => 'citra@bioskop.com'],
            [
                'role_id' => $customer->role_id,
                'name' => 'Citra',
                'password' => bcrypt('password'),
                'contact' => '085555555555'
            ]
        );

        $reviews = [
            // Avengers: Endgame Reviews
            [
                'user_id' => $customer->id,
                'film_id' => $avengers->id,
                'rating' => 5,
                'comment' => 'Film yang sangat luar biasa! Sangat emosional dan penutup saga yang sempurna.',
            ],
            [
                'user_id' => $budi->id,
                'film_id' => $avengers->id,
                'rating' => 4,
                'comment' => 'Sangat bagus, penutupan MCU fase 3 yang sangat epik dan memuaskan!',
            ],
            [
                'user_id' => $ani->id,
                'film_id' => $avengers->id,
                'rating' => 5,
                'comment' => 'Nangis banget nonton ini, perjuangan 11 tahun lunas! I love you 3000.',
            ],

            // Inception Reviews
            [
                'user_id' => $customer->id,
                'film_id' => $inception->id,
                'rating' => 5,
                'comment' => 'Karya agung Christopher Nolan. Konsep mimpi dalam mimpi yang sangat cerdas!',
            ],
            [
                'user_id' => $budi->id,
                'film_id' => $inception->id,
                'rating' => 5,
                'comment' => 'Alur ceritanya mind-bending banget. Musik Hans Zimmer juga luar biasa mantap!',
            ],
            [
                'user_id' => $citra->id,
                'film_id' => $inception->id,
                'rating' => 4,
                'comment' => 'Akting Leonardo DiCaprio luar biasa, konsepnya sangat orisinal dan menakjubkan.',
            ],

            // Interstellar Reviews (customer@bioskop.com has NO review here so they can manually review it)
            [
                'user_id' => $budi->id,
                'film_id' => $interstellar->id,
                'rating' => 5,
                'comment' => 'Film sci-fi terbaik sepanjang masa! Penjelasan ilmiah tentang blackhole sangat akurat.',
            ],
            [
                'user_id' => $ani->id,
                'film_id' => $interstellar->id,
                'rating' => 5,
                'comment' => 'Hubungan ayah dan anak yang sangat mengharukan. Scoring music organ gerejanya luar biasa megah.',
            ],
        ];

        foreach ($reviews as $review) {
            Review::updateOrCreate(
                [
                    'user_id' => $review['user_id'],
                    'film_id' => $review['film_id'],
                ],
                $review
            );
        }
    }
}
