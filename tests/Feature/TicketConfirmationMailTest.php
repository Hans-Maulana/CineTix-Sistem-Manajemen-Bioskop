<?php

namespace Tests\Feature;

use App\Mail\TicketConfirmationMail;
use App\Models\Booking;
use App\Models\Film;
use App\Models\Payment;
use App\Models\Role;
use App\Models\Schedule;
use App\Models\Seat;
use App\Models\Studio;
use App\Models\TicketBooking;
use App\Models\User;
use App\Services\Payment\VirtualAccountPaymentStrategy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class TicketConfirmationMailTest extends TestCase
{
    use RefreshDatabase;

    private function seedBooking(bool $asGuest, string $email): array
    {
        $role = Role::create(['name' => 'customer']);
        $user = User::create([
            'role_id' => $role->id,
            'name' => 'Member Test',
            'email' => 'member@example.com',
            'password' => bcrypt('password'),
            'contact' => '08123456789',
        ]);

        $studio = Studio::create(['name' => 'Studio A', 'capacity' => 50]);
        $seat = Seat::create([
            'studio_id' => $studio->id,
            'row_label' => 'C',
            'seat_number' => 7,
            'seat_code' => 'C7',
            'status' => 'available',
        ]);

        $film = Film::create([
            'title' => 'The Dark Knight',
            'synopsis' => 'Synopsis',
            'duration' => 152,
            'rating' => 0,
            'actors' => 'Actor',
            'director' => 'Director',
            'production' => 'Studio',
            'status' => 'now_playing',
            'classification' => '13+',
            'release_date' => now()->subMonth()->toDateString(),
        ]);

        $schedule = Schedule::create([
            'film_id' => $film->id,
            'studio_id' => $studio->id,
            'schedule_date' => now()->addDay()->toDateString(),
            'start_time' => '13:30:00',
            'end_time' => '16:00:00',
            'ticket_price' => 75000,
            'status' => 'on schedule',
        ]);

        $booking = Booking::create([
            'user_id' => $asGuest ? null : $user->id,
            'guest_email' => $asGuest ? $email : $user->email,
            'access_token' => $asGuest ? Str::random(64) : null,
            'schedule_id' => $schedule->id,
            'booking_type' => 'ticket',
            'total_amount' => 75000,
            'status' => 'pending',
            'qr_redeem' => Str::random(15),
        ]);

        TicketBooking::create([
            'booking_id' => $booking->id,
            'schedule_id' => $schedule->id,
            'seat_id' => $seat->id,
            'price_at_sale' => 75000,
        ]);

        $payment = (new VirtualAccountPaymentStrategy())->initiate($booking);

        return compact('booking', 'payment', 'user');
    }

    public function test_guest_receives_ticket_email_after_payment_confirmation(): void
    {
        Mail::fake();

        ['booking' => $booking, 'payment' => $payment] = $this->seedBooking(true, 'guest@example.com');

        $response = $this->post(route('booking.confirm-payment', [
            'booking' => $booking,
            'payment' => $payment,
            'token' => $booking->access_token,
        ]));

        $response->assertRedirect();

        Mail::assertSent(TicketConfirmationMail::class, function (TicketConfirmationMail $mail) {
            return $mail->hasTo('guest@example.com');
        });

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'confirmed',
        ]);
    }

    public function test_logged_in_user_receives_ticket_email_after_payment_confirmation(): void
    {
        Mail::fake();

        ['booking' => $booking, 'payment' => $payment, 'user' => $user] = $this->seedBooking(false, 'member@example.com');

        $response = $this->actingAs($user)->post(route('booking.confirm-payment', [
            'booking' => $booking,
            'payment' => $payment,
        ]));

        $response->assertRedirect();

        Mail::assertSent(TicketConfirmationMail::class, function (TicketConfirmationMail $mail) {
            return $mail->hasTo('member@example.com');
        });
    }

    public function test_resend_ticket_email_for_confirmed_guest_booking(): void
    {
        Mail::fake();

        ['booking' => $booking] = $this->seedBooking(true, 'guest@example.com');
        $booking->update(['status' => 'confirmed']);

        $response = $this->post(route('booking.resend-ticket', [
            'booking' => $booking,
            'token' => $booking->access_token,
        ]));

        $response->assertRedirect();
        $response->assertSessionHas('ticket_email_sent', true);

        Mail::assertSent(TicketConfirmationMail::class, function (TicketConfirmationMail $mail) {
            return $mail->hasTo('guest@example.com');
        });
    }

    public function test_confirm_payment_resends_email_when_payment_already_successful(): void
    {
        Mail::fake();

        ['booking' => $booking, 'payment' => $payment] = $this->seedBooking(true, 'guest@example.com');
        $booking->update(['status' => 'confirmed']);
        $payment->update(['status' => 'success', 'paid_at' => now()]);

        $response = $this->post(route('booking.confirm-payment', [
            'booking' => $booking,
            'payment' => $payment,
            'token' => $booking->access_token,
        ]));

        $response->assertRedirect();
        Mail::assertSent(TicketConfirmationMail::class);
    }
}
