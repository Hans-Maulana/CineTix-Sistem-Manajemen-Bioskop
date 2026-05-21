<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Film;
use App\Models\Payment;
use App\Models\Role;
use App\Models\Schedule;
use App\Models\Seat;
use App\Models\Studio;
use App\Models\TicketBooking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BookingReviewTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;
    private Film $film;
    private Studio $studio;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Seed Roles
        Role::create(['name' => 'admin']);
        $customerRole = Role::create(['name' => 'customer']);

        // 2. Create customer user
        $this->customer = User::create([
            'role_id'  => $customerRole->id,
            'name'     => 'John Doe',
            'email'    => 'john@example.com',
            'password' => bcrypt('password'),
            'contact'  => '082222222222',
        ]);

        // 3. Create Studio & Film
        $this->studio = Studio::create([
            'name'     => 'Studio A',
            'capacity' => 50,
        ]);

        // Create seats for Studio A
        for ($i = 1; $i <= 10; $i++) {
            Seat::create([
                'studio_id' => $this->studio->id,
                'row_label' => 'A',
                'seat_number' => $i,
                'seat_code' => 'A' . $i,
                'status' => 'available',
            ]);
        }

        $this->film = Film::create([
            'title'          => 'Avengers: Endgame',
            'synopsis'       => 'Saga finale',
            'duration'       => 180,
            'rating'         => 0,
            'actors'         => 'Robert Downey Jr.',
            'director'       => 'Russo Brothers',
            'production'     => 'Marvel Studios',
            'status'         => 'now_playing',
            'classification' => 'SU',
            'release_date'   => '2019-04-26',
        ]);
    }

    /**
     * Test review flow for past confirmed booking vs future booking vs pending/cancelled booking.
     */
    public function test_review_visibility_and_submission_flows(): void
    {
        // --- 1. PAST CONFIRMED BOOKING (Eligible for Review) ---
        // Create past complete schedule (yesterday)
        $pastSchedule = Schedule::create([
            'film_id'       => $this->film->id,
            'studio_id'     => $this->studio->id,
            'schedule_date' => now()->subDay()->toDateString(),
            'start_time'    => '10:00:00',
            'end_time'      => '13:00:00',
            'ticket_price'  => 50000,
            'status'        => 'complete',
        ]);

        // Create confirmed booking
        $bookingPast = Booking::create([
            'user_id' => $this->customer->id,
            'schedule_id' => $pastSchedule->id,
            'booking_type' => 'ticket',
            'total_amount' => 50000,
            'status' => 'confirmed',
            'qr_redeem' => 'some-uuid-string',
            'status_redeem' => 'unredeemed',
        ]);

        $seat = Seat::where('studio_id', $this->studio->id)->first();
        TicketBooking::create([
            'booking_id' => $bookingPast->id,
            'schedule_id' => $pastSchedule->id,
            'seat_id' => $seat->id,
            'price_at_sale' => 50000,
        ]);

        Payment::create([
            'booking_id' => $bookingPast->id,
            'amount' => 50000,
            'method' => 'qris',
            'status' => 'success',
            'countdown_seconds' => 300,
            'paid_at' => now()->subDay(),
        ]);

        // --- 2. FUTURE CONFIRMED BOOKING (NOT Eligible for Review yet) ---
        $futureSchedule = Schedule::create([
            'film_id'       => $this->film->id,
            'studio_id'     => $this->studio->id,
            'schedule_date' => now()->addDay()->toDateString(),
            'start_time'    => '14:00:00',
            'end_time'      => '17:00:00',
            'ticket_price'  => 50000,
            'status'        => 'on schedule',
        ]);

        $bookingFuture = Booking::create([
            'user_id' => $this->customer->id,
            'schedule_id' => $futureSchedule->id,
            'booking_type' => 'ticket',
            'total_amount' => 50000,
            'status' => 'confirmed',
            'qr_redeem' => 'another-uuid-string',
            'status_redeem' => 'unredeemed',
        ]);

        $seat2 = Seat::where('studio_id', $this->studio->id)->skip(1)->first();
        TicketBooking::create([
            'booking_id' => $bookingFuture->id,
            'schedule_id' => $futureSchedule->id,
            'seat_id' => $seat2->id,
            'price_at_sale' => 50000,
        ]);

        Payment::create([
            'booking_id' => $bookingFuture->id,
            'amount' => 50000,
            'method' => 'qris',
            'status' => 'success',
            'countdown_seconds' => 300,
            'paid_at' => now(),
        ]);

        // Log in as customer
        $this->actingAs($this->customer);

        // --- 3. VERIFY HISTORY PAGE VISIBILITY ---
        $response = $this->get(route('booking.history'));
        $response->assertStatus(200);

        // Should see the "Tulis Ulasan" button for the past booking
        $response->assertSee('Tulis Ulasan');
        $response->assertSee('review-form-' . $bookingPast->id);

        // Should NOT see a review button for the future booking
        $response->assertDontSee('review-form-' . $bookingFuture->id);

        // --- 4. SUBMIT REVIEW FOR PAST BOOKING ---
        $reviewResponse = $this->post(route('booking.store-review'), [
            'film_id' => $this->film->id,
            'rating' => 5,
            'comment' => 'Sangat memuaskan!',
        ]);

        $reviewResponse->assertRedirect();
        $this->assertDatabaseHas('reviews', [
            'user_id' => $this->customer->id,
            'film_id' => $this->film->id,
            'rating' => 5,
            'comment' => 'Sangat memuaskan!',
        ]);

        // --- 5. VERIFY HISTORY PAGE SHOWS COMPLETED REVIEW ---
        $responseAfterReview = $this->get(route('booking.history'));
        $responseAfterReview->assertStatus(200);

        // "Tulis Ulasan" button should be gone now for the past booking since we reviewed it
        $responseAfterReview->assertDontSee('review-form-' . $bookingPast->id);
        $responseAfterReview->assertSee('Ulasan Anda');
        $responseAfterReview->assertSee('Sangat memuaskan!');

        // --- 6. VERIFY REVIEW APPEARS ON FILM DETAILS PAGE ---
        $filmDetailsResponse = $this->get(route('films.detail', $this->film));
        $filmDetailsResponse->assertStatus(200);
        $filmDetailsResponse->assertSee('John Doe');
        $filmDetailsResponse->assertSee('Sangat memuaskan!');

        // --- 7. ATTEMPT TO SUBMIT A FUTURE BOOKING REVIEW (SHOULD BE BLOCKED) ---
        // Note: The review controller checks if there is any confirmed ticket for a past schedule date.
        // If we try to review a film for which we only have future schedules, it should block.
        // Let's create a new film for which we only have a future booking.
        $futureFilm = Film::create([
            'title'          => 'Future Film',
            'synopsis'       => 'Not released yet',
            'duration'       => 120,
            'rating'         => 0,
            'actors'         => 'Actor',
            'director'       => 'Director',
            'production'     => 'Prod',
            'status'         => 'now_playing',
            'classification' => 'SU',
            'release_date'   => '2026-01-01',
        ]);

        $futureFilmSchedule = Schedule::create([
            'film_id'       => $futureFilm->id,
            'studio_id'     => $this->studio->id,
            'schedule_date' => now()->addDays(5)->toDateString(),
            'start_time'    => '10:00:00',
            'end_time'      => '12:00:00',
            'ticket_price'  => 50000,
            'status'        => 'on schedule',
        ]);

        $bookingOnlyFuture = Booking::create([
            'user_id' => $this->customer->id,
            'schedule_id' => $futureFilmSchedule->id,
            'booking_type' => 'ticket',
            'total_amount' => 50000,
            'status' => 'confirmed',
        ]);

        $seat3 = Seat::where('studio_id', $this->studio->id)->skip(2)->first();
        TicketBooking::create([
            'booking_id' => $bookingOnlyFuture->id,
            'schedule_id' => $futureFilmSchedule->id,
            'seat_id' => $seat3->id,
            'price_at_sale' => 50000,
        ]);

        // Attempting to post review for the future-only film should fail
        $invalidReviewResponse = $this->post(route('booking.store-review'), [
            'film_id' => $futureFilm->id,
            'rating' => 4,
            'comment' => 'Curang coba ulas duluan',
        ]);

        $invalidReviewResponse->assertSessionHas('error');
        $this->assertDatabaseMissing('reviews', [
            'film_id' => $futureFilm->id,
        ]);
    }
}
