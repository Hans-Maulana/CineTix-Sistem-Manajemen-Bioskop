<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\TicketBooking;
use App\Models\Schedule;
use App\Models\Seat;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Dapatkan customer user
        $customer = User::where('email', 'customer@bioskop.com')->first();
        if (!$customer) {
            $this->command->error('User customer@bioskop.com tidak ditemukan. Jalankan CustomerSeeder terlebih dahulu.');
            return;
        }

        // 2. Dapatkan schedule yang sesuai
        $pastScheduleAvengers = Schedule::whereHas('film', function ($q) {
            $q->where('title', 'Avengers: Endgame');
        })->where('status', 'complete')->first();

        $pastScheduleInception = Schedule::whereHas('film', function ($q) {
            $q->where('title', 'Inception');
        })->where('status', 'complete')->first();

        $pastScheduleInterstellar = Schedule::whereHas('film', function ($q) {
            $q->where('title', 'Interstellar');
        })->where('status', 'complete')->first();

        $futureScheduleInception = Schedule::whereHas('film', function ($q) {
            $q->where('title', 'Inception');
        })->where('status', 'on schedule')->first();

        if (!$pastScheduleAvengers || !$pastScheduleInception || !$pastScheduleInterstellar || !$futureScheduleInception) {
            $this->command->error('Schedules tidak lengkap. Jalankan ScheduleSeeder terlebih dahulu.');
            return;
        }

        // Dapatkan kursi-kursi untuk studio masing-masing
        $seatsAvengers = Seat::where('studio_id', $pastScheduleAvengers->studio_id)->take(2)->get();
        $seatsInceptionPast = Seat::where('studio_id', $pastScheduleInception->studio_id)->take(2)->get();
        $seatsInterstellar = Seat::where('studio_id', $pastScheduleInterstellar->studio_id)->take(2)->get();
        $seatsInceptionFuturePending = Seat::where('studio_id', $futureScheduleInception->studio_id)->skip(2)->take(2)->get();
        $seatsInceptionFutureCancelled = Seat::where('studio_id', $futureScheduleInception->studio_id)->skip(4)->take(2)->get();

        // --- 1. SEED BOOKING BERHASIL MASA LALU (Avengers: Endgame) ---
        if ($seatsAvengers->count() >= 2) {
            $ticketPrice = $pastScheduleAvengers->ticket_price;
            $totalAmount = $ticketPrice * 2;

            $booking = Booking::create([
                'user_id' => $customer->id,
                'schedule_id' => $pastScheduleAvengers->id,
                'booking_type' => 'ticket',
                'total_amount' => $totalAmount,
                'status' => 'confirmed',
                'qr_redeem' => (string) Str::uuid(),
                'status_redeem' => 'unredeemed',
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ]);

            foreach ($seatsAvengers as $seat) {
                TicketBooking::create([
                    'booking_id' => $booking->id,
                    'schedule_id' => $pastScheduleAvengers->id,
                    'seat_id' => $seat->id,
                    'price_at_sale' => $ticketPrice,
                ]);

                // Update seat status to booked (global fallback, but database checkAvailability handles schedule check)
                $seat->update(['status' => 'booked']);
            }

            Payment::create([
                'booking_id' => $booking->id,
                'amount' => $totalAmount,
                'method' => 'virtual_account',
                'status' => 'success',
                'va_number' => '1234567890123456',
                'countdown_seconds' => 300,
                'paid_at' => Carbon::now()->subDays(2)->addMinutes(5),
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2)->addMinutes(5),
            ]);
        }

        // --- 2. SEED BOOKING BERHASIL MASA LALU (Inception) ---
        if ($seatsInceptionPast->count() >= 2) {
            $ticketPrice = $pastScheduleInception->ticket_price;
            $totalAmount = $ticketPrice * 2;

            $booking = Booking::create([
                'user_id' => $customer->id,
                'schedule_id' => $pastScheduleInception->id,
                'booking_type' => 'ticket',
                'total_amount' => $totalAmount,
                'status' => 'confirmed',
                'qr_redeem' => (string) Str::uuid(),
                'status_redeem' => 'redeemed', // Sudah ditukarkan tiketnya
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subDays(1),
            ]);

            foreach ($seatsInceptionPast as $seat) {
                TicketBooking::create([
                    'booking_id' => $booking->id,
                    'schedule_id' => $pastScheduleInception->id,
                    'seat_id' => $seat->id,
                    'price_at_sale' => $ticketPrice,
                ]);

                $seat->update(['status' => 'booked']);
            }

            Payment::create([
                'booking_id' => $booking->id,
                'amount' => $totalAmount,
                'method' => 'qris',
                'status' => 'success',
                'countdown_seconds' => 300,
                'paid_at' => Carbon::now()->subDays(1)->addMinutes(3),
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subDays(1)->addMinutes(3),
            ]);
        }

        // --- 3. SEED BOOKING PENDING MASA DEPAN (Inception) ---
        if ($seatsInceptionFuturePending->count() >= 2) {
            $ticketPrice = $futureScheduleInception->ticket_price;
            $totalAmount = $ticketPrice * 2;

            $booking = Booking::create([
                'user_id' => $customer->id,
                'schedule_id' => $futureScheduleInception->id,
                'booking_type' => 'ticket',
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'qr_redeem' => null,
                'status_redeem' => 'unredeemed',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            foreach ($seatsInceptionFuturePending as $seat) {
                TicketBooking::create([
                    'booking_id' => $booking->id,
                    'schedule_id' => $futureScheduleInception->id,
                    'seat_id' => $seat->id,
                    'price_at_sale' => $ticketPrice,
                ]);

                // Pending bookings lock the seat (status pending, locked_until)
                $seat->update([
                    'status' => 'pending',
                    'locked_until' => Carbon::now()->addSeconds(300),
                    'locked_by_user_id' => $customer->id,
                ]);
            }

            Payment::create([
                'booking_id' => $booking->id,
                'amount' => $totalAmount,
                'method' => 'virtual_account',
                'status' => 'pending',
                'va_number' => '9876543210987654',
                'countdown_seconds' => 300,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        // --- 4. SEED BOOKING CANCELLED MASA DEPAN (Inception) ---
        if ($seatsInceptionFutureCancelled->count() >= 2) {
            $ticketPrice = $futureScheduleInception->ticket_price;
            $totalAmount = $ticketPrice * 2;

            $booking = Booking::create([
                'user_id' => $customer->id,
                'schedule_id' => $futureScheduleInception->id,
                'booking_type' => 'ticket',
                'total_amount' => $totalAmount,
                'status' => 'cancelled',
                'qr_redeem' => null,
                'status_redeem' => 'unredeemed',
                'created_at' => Carbon::now()->subHours(6),
                'updated_at' => Carbon::now()->subHours(6),
            ]);

            foreach ($seatsInceptionFutureCancelled as $seat) {
                TicketBooking::create([
                    'booking_id' => $booking->id,
                    'schedule_id' => $futureScheduleInception->id,
                    'seat_id' => $seat->id,
                    'price_at_sale' => $ticketPrice,
                ]);

                // Cancelled bookings release the seat
                $seat->update([
                    'status' => 'available',
                    'locked_until' => null,
                    'locked_by_user_id' => null,
                ]);
            }

            Payment::create([
                'booking_id' => $booking->id,
                'amount' => $totalAmount,
                'method' => 'qris',
                'status' => 'failed',
                'countdown_seconds' => 300,
                'created_at' => Carbon::now()->subHours(6),
                'updated_at' => Carbon::now()->subHours(6),
            ]);
        }

        // --- 5. SEED BOOKING BERHASIL MASA LALU (Interstellar - Untuk dicoba Ulas sendiri oleh User) ---
        if ($seatsInterstellar->count() >= 2) {
            $ticketPrice = $pastScheduleInterstellar->ticket_price;
            $totalAmount = $ticketPrice * 2;

            $booking = Booking::create([
                'user_id' => $customer->id,
                'schedule_id' => $pastScheduleInterstellar->id,
                'booking_type' => 'ticket',
                'total_amount' => $totalAmount,
                'status' => 'confirmed',
                'qr_redeem' => (string) Str::uuid(),
                'status_redeem' => 'unredeemed',
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(3),
            ]);

            foreach ($seatsInterstellar as $seat) {
                TicketBooking::create([
                    'booking_id' => $booking->id,
                    'schedule_id' => $pastScheduleInterstellar->id,
                    'seat_id' => $seat->id,
                    'price_at_sale' => $ticketPrice,
                ]);

                $seat->update(['status' => 'booked']);
            }

            Payment::create([
                'booking_id' => $booking->id,
                'amount' => $totalAmount,
                'method' => 'virtual_account',
                'status' => 'success',
                'va_number' => '1122334455667788',
                'countdown_seconds' => 300,
                'paid_at' => Carbon::now()->subDays(3)->addMinutes(5),
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(3)->addMinutes(5),
            ]);
        }
    }
}
