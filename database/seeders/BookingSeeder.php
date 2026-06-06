<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\TicketBooking;
use App\Models\Schedule;
use App\Models\Seat;
use App\Models\User;
use App\Models\Film;
use App\Models\Studio;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks to truncate cleanly
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        Booking::truncate();
        TicketBooking::truncate();
        Payment::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        // 1. Get films, studios, and customer users
        $films = Film::all();
        $studios = Studio::all();
        $customers = User::whereHas('role', function($q) {
            $q->where('name', 'customer');
        })->get();

        if ($films->isEmpty() || $studios->isEmpty() || $customers->isEmpty()) {
            $this->command->error('Pastikan Film, Studio, dan Customer sudah di-seed!');
            return;
        }

        $this->command->info('Memulai seeding data transaksi dummy (150 transaksi)...');

        // Let's generate 150 bookings spread over the last 12 months
        for ($i = 0; $i < 150; $i++) {
            $isGuest = rand(1, 100) <= 30; // 30% guest
            $film = $films->random();
            $studio = $studios->random();
            
            // Generate a date within the last 365 days
            $daysAgo = rand(1, 365);
            $createdAt = Carbon::now()->subDays($daysAgo)->subHours(rand(1, 23))->subMinutes(rand(1, 59));
            
            // Create a schedule for this booking
            $startTime = Carbon::parse($createdAt->toDateString() . ' ' . rand(10, 21) . ':00:00');
            $endTime = (clone $startTime)->addMinutes($film->duration);
            
            $schedule = Schedule::create([
                'film_id' => $film->id,
                'studio_id' => $studio->id,
                'schedule_date' => $createdAt->toDateString(),
                'start_time' => $startTime->format('H:i:s'),
                'end_time' => $endTime->format('H:i:s'),
                'ticket_price' => rand(35, 75) * 1000,
                'status' => 'complete',
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            // Choose 1-4 seats
            $seats = Seat::where('studio_id', $studio->id)->inRandomOrder()->take(rand(1, 4))->get();
            if ($seats->isEmpty()) continue;

            $ticketPrice = $schedule->ticket_price;
            $totalAmount = $ticketPrice * $seats->count();

            // Status distribution
            $randStatus = rand(1, 100);
            if ($randStatus <= 85) {
                $bookingStatus = 'confirmed';
                $paymentStatus = 'success';
            } elseif ($randStatus <= 95) {
                $bookingStatus = 'pending';
                $paymentStatus = 'pending';
            } else {
                $bookingStatus = 'cancelled';
                $paymentStatus = 'failed';
            }

            $guestEmail = null;
            $guestName = null;
            $userId = null;
            if ($isGuest) {
                $guestNames = ['Budi', 'Joko', 'Andi', 'Siti', 'Rini', 'Agus', 'Dewi', 'Hendra', 'Wati', 'Tono'];
                $guestName = $guestNames[array_rand($guestNames)] . ' ' . rand(10, 99);
                $guestEmail = strtolower(str_replace(' ', '', $guestName)) . '@example.com';
            } else {
                $userId = $customers->random()->id;
            }

            $booking = Booking::create([
                'user_id' => $userId,
                'guest_name' => $guestName,
                'guest_email' => $guestEmail,
                'schedule_id' => $schedule->id,
                'booking_type' => 'ticket',
                'total_amount' => $totalAmount,
                'status' => $bookingStatus,
                'qr_redeem' => $bookingStatus === 'confirmed' ? (string) Str::uuid() : null,
                'status_redeem' => $bookingStatus === 'confirmed' && rand(1, 100) <= 60 ? 'redeemed' : 'unredeemed',
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            foreach ($seats as $seat) {
                TicketBooking::create([
                    'booking_id' => $booking->id,
                    'schedule_id' => $schedule->id,
                    'seat_id' => $seat->id,
                    'price_at_sale' => $ticketPrice,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }

            $methods = ['qris', 'virtual_account', 'credit_card'];
            Payment::create([
                'booking_id' => $booking->id,
                'amount' => $totalAmount,
                'method' => $methods[array_rand($methods)],
                'status' => $paymentStatus,
                'va_number' => $paymentStatus === 'pending' ? '9876' . rand(100000, 999999) : null,
                'paid_at' => $paymentStatus === 'success' ? (clone $createdAt)->addMinutes(rand(1, 10)) : null,
                'countdown_seconds' => 300,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }

        $this->command->info('Seeding data transaksi dummy selesai!');
    }
}
