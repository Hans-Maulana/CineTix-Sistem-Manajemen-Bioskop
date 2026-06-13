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
use App\Support\GuestBookingAccess;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentDuplicatePendingTest extends TestCase
{
    use RefreshDatabase;

    private Booking $booking;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['name' => 'customer']);
        $user = User::create([
            'role_id' => $role->id,
            'name' => 'Guest User',
            'email' => 'guest@example.com',
            'password' => bcrypt('password'),
            'contact' => '08123456789',
        ]);

        $studio = Studio::create(['name' => 'Studio A', 'capacity' => 50]);
        $seat = Seat::create([
            'studio_id' => $studio->id,
            'row_label' => 'A',
            'seat_number' => 1,
            'seat_code' => 'A1',
            'status' => 'pending',
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

        $schedule = Schedule::create([
            'film_id' => $film->id,
            'studio_id' => $studio->id,
            'schedule_date' => now()->addDay()->toDateString(),
            'start_time' => '14:00:00',
            'end_time' => '16:00:00',
            'ticket_price' => 50000,
            'status' => 'on schedule',
        ]);

        $this->booking = Booking::create([
            'user_id' => null,
            'guest_email' => 'guest@example.com',
            'access_token' => 'test-token',
            'schedule_id' => $schedule->id,
            'booking_type' => 'ticket',
            'total_amount' => 50000,
            'status' => 'pending',
            'qr_redeem' => 'QR123',
        ]);

        GuestBookingAccess::grant($this->booking);

        TicketBooking::create([
            'booking_id' => $this->booking->id,
            'schedule_id' => $schedule->id,
            'seat_id' => $seat->id,
            'price_at_sale' => 50000,
        ]);
    }

    public function test_confirm_payment_succeeds_when_duplicate_pending_exists(): void
    {
        $olderPayment = Payment::create([
            'booking_id' => $this->booking->id,
            'amount' => 50000,
            'method' => 'qris',
            'status' => 'pending',
            'countdown_seconds' => 300,
        ]);

        $currentPayment = Payment::create([
            'booking_id' => $this->booking->id,
            'amount' => 50000,
            'method' => 'qris',
            'status' => 'pending',
            'countdown_seconds' => 300,
        ]);

        $response = $this->post(route('booking.confirm-payment', [
            'booking' => $this->booking,
            'payment' => $currentPayment,
            'token' => 'test-token',
        ]));

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $this->assertSame('success', $currentPayment->fresh()->status);
        $this->assertSame('failed', $olderPayment->fresh()->status);
        $this->assertSame('confirmed', $this->booking->fresh()->status);
    }

    public function test_initiate_payment_reuses_active_pending_for_same_method(): void
    {
        $existingPayment = Payment::create([
            'booking_id' => $this->booking->id,
            'amount' => 50000,
            'method' => 'qris',
            'status' => 'pending',
            'countdown_seconds' => 300,
        ]);

        $response = $this->post(route('booking.initiate-payment', [
            'booking' => $this->booking,
            'token' => 'test-token',
        ]), [
            'payment_method' => 'qris',
        ]);

        $response->assertRedirect(route('booking.process-payment', [
            'booking' => $this->booking,
            'payment' => $existingPayment,
            'token' => 'test-token',
        ], false));

        $this->assertSame(1, Payment::where('booking_id', $this->booking->id)->count());
    }
}
