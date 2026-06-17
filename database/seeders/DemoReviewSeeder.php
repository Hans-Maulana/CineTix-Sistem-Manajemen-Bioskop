<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Film;
use App\Models\Studio;
use App\Models\Schedule;
use App\Models\Booking;
use App\Models\TicketBooking;
use App\Models\Payment;
use App\Models\Seat;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DemoReviewSeeder extends Seeder
{
    public function run(): void
    {
        $emails = ['2472020@maranatha.ac.id', 'customer@bioskop.com'];
        $users = User::whereIn('email', $emails)->get();

        if ($users->isEmpty()) {
            $this->command->error('Users not found.');
            return;
        }

        $film = Film::first();
        $studio = Studio::first();

        // Create a past schedule
        $yesterday = Carbon::yesterday()->setHour(19)->setMinute(0)->setSecond(0);
        $schedule = Schedule::create([
            'film_id' => $film->id,
            'studio_id' => $studio->id,
            'schedule_date' => $yesterday->toDateString(),
            'start_time' => $yesterday->format('H:i:s'),
            'end_time' => (clone $yesterday)->addMinutes($film->duration)->format('H:i:s'),
            'ticket_price' => 50000,
            'status' => 'complete',
            'created_at' => $yesterday->copy()->subDays(2),
            'updated_at' => $yesterday->copy()->subDays(2),
        ]);

        $seats = Seat::where('studio_id', $studio->id)->take($users->count() * 2)->get();
        $seatIndex = 0;

        foreach ($users as $user) {
            $booking = Booking::create([
                'user_id' => $user->id,
                'schedule_id' => $schedule->id,
                'booking_type' => 'ticket',
                'total_amount' => 100000,
                'status' => 'completed', // Or 'confirmed' + 'redeemed'
                'qr_redeem' => (string) Str::uuid(),
                'status_redeem' => 'redeemed',
                'created_at' => $yesterday->copy()->subDays(2),
                'updated_at' => $yesterday->copy()->subDays(2),
            ]);

            for ($i = 0; $i < 2; $i++) {
                if (isset($seats[$seatIndex])) {
                    TicketBooking::create([
                        'booking_id' => $booking->id,
                        'schedule_id' => $schedule->id,
                        'seat_id' => $seats[$seatIndex]->id,
                        'price_at_sale' => 50000,
                        'created_at' => $yesterday->copy()->subDays(2),
                        'updated_at' => $yesterday->copy()->subDays(2),
                    ]);
                    $seatIndex++;
                }
            }

            Payment::create([
                'booking_id' => $booking->id,
                'amount' => 100000,
                'method' => 'qris',
                'status' => 'success',
                'paid_at' => $yesterday->copy()->subDays(2)->addMinutes(5),
                'countdown_seconds' => 300,
                'created_at' => $yesterday->copy()->subDays(2),
                'updated_at' => $yesterday->copy()->subDays(2),
            ]);
        }

        $this->command->info('Seeder Demo Review berhasil dijalankan! Silakan cek menu Riwayat Transaksi / Tiket Aktif dengan akun 2472020@maranatha.ac.id atau customer@bioskop.com');
    }
}
