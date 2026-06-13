<?php

namespace Tests\Feature;

use App\Models\Film;
use App\Models\Role;
use App\Models\Schedule;
use App\Models\Seat;
use App\Models\Studio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class BookingStoreTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;
    private Schedule $schedule;
    private Seat $seat;

    protected function setUp(): void
    {
        parent::setUp();

        $customerRole = Role::create(['name' => 'customer']);

        $this->customer = User::create([
            'role_id' => $customerRole->id,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
            'contact' => '082222222222',
        ]);

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
            'status' => 'active',
        ]);
    }

    public function test_authenticated_user_can_store_booking_and_redirect_to_payment(): void
    {
        $response = $this->actingAs($this->customer)->post(route('booking.store'), [
            'schedule_id' => $this->schedule->id,
            'seat_ids' => [$this->seat->id],
            'guest_email' => $this->customer->email,
        ]);

        $response->assertRedirect();
        $this->assertStringContainsString('/booking/payment/', $response->headers->get('Location'));

        $this->assertDatabaseHas('bookings', [
            'user_id' => $this->customer->id,
            'schedule_id' => $this->schedule->id,
            'status' => 'pending',
        ]);
    }

    public function test_guest_must_verify_otp_before_booking(): void
    {
        $response = $this->post(route('booking.store'), [
            'schedule_id' => $this->schedule->id,
            'seat_ids' => [$this->seat->id],
            'guest_email' => 'guest@example.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Email belum diverifikasi via OTP. Silakan verifikasi ulang.');
    }

    public function test_verified_guest_can_store_booking_and_redirect_to_payment(): void
    {
        $email = 'guest@example.com';
        Cache::put('guest_otp_' . $email, 123456, now()->addMinutes(5));

        $this->postJson(route('guest.verify-otp'), [
            'email' => $email,
            'otp' => 123456,
        ])->assertOk()->assertJson(['success' => true]);

        $response = $this->post(route('booking.store'), [
            'schedule_id' => $this->schedule->id,
            'seat_ids' => [$this->seat->id],
            'guest_email' => $email,
        ]);

        $response->assertRedirect();
        $this->assertStringContainsString('/booking/payment/', $response->headers->get('Location'));
        $this->assertStringContainsString('token=', $response->headers->get('Location'));

        $this->assertDatabaseHas('bookings', [
            'user_id' => null,
            'guest_email' => $email,
            'status' => 'pending',
        ]);
    }
}
