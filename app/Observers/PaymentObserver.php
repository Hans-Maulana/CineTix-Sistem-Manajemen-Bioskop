<?php

namespace App\Observers;

use App\Models\Payment;
use App\Models\TicketBooking;
use App\Events\SeatStatusUpdated;
use Illuminate\Support\Facades\Log;

class PaymentObserver
{
    /**
     * Handle the Payment "updated" event.
     */
    public function updated(Payment $payment): void
    {
        // Jika payment berubah status menjadi 'failed', kembalikan kursi ke 'available'
        if ($payment->isDirty('status') && $payment->status === 'failed') {
            $this->returnSeatsToAvailable($payment);
        }
    }

    /**
     * Handle the Payment "saved" event.
     */
    public function saved(Payment $payment): void
    {
        // Jika payment baru dibuat dengan status 'failed', kembalikan kursi
        if ($payment->wasRecentlyCreated && $payment->status === 'failed') {
            $this->returnSeatsToAvailable($payment);
        }
    }

    /**
     * Kembalikan kursi-kursi booking ke status 'available'
     */
    private function returnSeatsToAvailable(Payment $payment): void
    {
        try {
            // Dapatkan semua ticket booking yang terkait dengan booking ini
            $ticketBookings = TicketBooking::where('booking_id', $payment->booking_id)
                ->with('seat')
                ->get();

            foreach ($ticketBookings as $ticketBooking) {
                $seat = $ticketBooking->seat;
                
                // Update status kursi kembali ke 'available'
                $seat->update([
                    'status' => 'available',
                    'locked_until' => null,
                    'locked_by_user_id' => null,
                ]);

                // Broadcast event untuk memberitahu user lain bahwa kursi sudah tersedia
                try {
                    broadcast(new SeatStatusUpdated($seat->id, 'available'))->toOthers();
                } catch (\Exception $e) {
                    Log::error("Pusher Error (PaymentObserver): " . $e->getMessage());
                }
            }

            Log::info("Kursi berhasil dikembalikan ke available untuk booking {$payment->booking_id}");
        } catch (\Exception $e) {
            Log::error("Error returning seats for payment {$payment->id}: " . $e->getMessage());
        }
    }
}
