<?php

namespace Tests\Feature;

use App\Models\Film;
use App\Models\Schedule;
use App\Models\Seat;
use App\Models\Studio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RememberSeatsTest extends TestCase
{
    use RefreshDatabase;

    private Schedule $schedule;

    private Seat $seat;

    protected function setUp(): void
    {
        parent::setUp();

        $studio = Studio::create([
            'name' => 'Studio A',
            'capacity' => 50,
        ]);

        $this->seat = Seat::create([
            'studio_id' => $studio->id,
            'row_label' => 'A',
            'seat_number' => 1,
            'seat_code' => 'A1',
            'status' => 'available',
        ]);

        $film = Film::create([
            'title' => 'Test Film',
            'synopsis' => 'Synopsis',
            'duration' => 120,
            'rating' => 0,
            'actors' => 'Actor',
            'director' => 'Director',
            'production' => 'Studio',
            'status' => 'now_playing',
            'classification' => 'SU',
            'release_date' => now()->subMonth()->toDateString(),
        ]);

        $this->schedule = Schedule::create([
            'film_id' => $film->id,
            'studio_id' => $studio->id,
            'schedule_date' => now()->addDay()->toDateString(),
            'start_time' => '14:00:00',
            'end_time' => '16:00:00',
            'ticket_price' => 50000,
            'status' => 'on schedule',
        ]);
    }

    public function test_guest_can_remember_seats_for_schedule(): void
    {
        $response = $this->postJson(route('booking.remember-seats', $this->schedule), [
            'seat_ids' => [$this->seat->id],
        ]);

        $response->assertOk()->assertJson(['success' => true]);

        $this->assertSame(
            [$this->seat->id],
            session('pending_seat_selection.' . $this->schedule->id)['seat_ids']
        );
    }

    public function test_booking_page_includes_restored_seats_after_login_return(): void
    {
        $this->withSession([
            'pending_seat_selection.' . $this->schedule->id => [
                'seat_ids' => [$this->seat->id],
                'saved_at' => now()->timestamp,
            ],
            'seat_restore_notice.' . $this->schedule->id => true,
        ]);

        $response = $this->get(route('booking.show', $this->schedule));

        $response->assertOk();
        $response->assertSee('serverRestoredSeats', false);
        $response->assertSee('"id":' . $this->seat->id, false);
        $response->assertSee('Pilihan kursi Anda (1) sudah dipulihkan setelah login.');
    }
}
