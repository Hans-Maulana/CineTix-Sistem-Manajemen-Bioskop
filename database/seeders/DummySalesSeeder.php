<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Schedule;
use App\Models\Seat;
use App\Models\TicketBooking;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DummySalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::whereHas('role', function($q) {
            $q->where('name', 'customer');
        })->get();
        
        if ($users->isEmpty()) {
            $users = User::all();
        }

        // Get schedules that are currently active or complete
        $schedules = Schedule::whereIn('status', ['on schedule', 'now playing', 'complete'])->get();

        foreach ($schedules as $schedule) {
            // Seed 1 to 5 bookings per schedule
            $bookingCount = rand(1, 5);
            
            for ($i = 0; $i < $bookingCount; $i++) {
                $user = $users->random();
                $seats = Seat::where('studio_id', $schedule->studio_id)->inRandomOrder()->limit(rand(1, 4))->get();
                
                if ($seats->isEmpty()) continue;

                $totalAmount = $seats->count() * $schedule->ticket_price;

                $status = 'confirmed';
                $refundStatus = null;
                $isRefund = rand(1, 20) === 1; // 5% chance of refund

                if ($isRefund) {
                    $refundStatus = rand(0, 1) ? 'requested' : 'approved';
                    if ($refundStatus === 'approved') {
                        $status = 'refunded';
                    }
                }

                $bookingDate = $schedule->schedule_date->copy()->subDays(rand(0, 5));

                $booking = Booking::create([
                    'user_id' => $user->id,
                    'schedule_id' => $schedule->id,
                    'booking_type' => 'online',
                    'total_amount' => $totalAmount,
                    'status' => $status,
                    'qr_redeem' => Str::random(10),
                    'status_redeem' => rand(0, 1) ? 'redeemed' : 'unredeemed',
                    'refund_amount' => $status === 'refunded' ? $totalAmount * 0.9 : null,
                    'created_at' => $bookingDate,
                    'updated_at' => $bookingDate,
                ]);

                foreach ($seats as $seat) {
                    TicketBooking::create([
                        'booking_id' => $booking->id,
                        'schedule_id' => $schedule->id,
                        'seat_id' => $seat->id,
                        'price_at_sale' => $schedule->ticket_price,
                    ]);
                }

                // Payment
                Payment::create([
                    'booking_id' => $booking->id,
                    'method' => 'qris',
                    'amount' => $totalAmount,
                    'status' => 'success',
                    'created_at' => $bookingDate,
                    'updated_at' => $bookingDate,
                ]);
            }
        }
    }
}
