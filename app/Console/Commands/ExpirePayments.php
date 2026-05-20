<?php

namespace App\Console\Commands;

use App\Models\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExpirePayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark expired pending payments as failed and return seats to available';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // Cari semua payment pending yang sudah expired
            $expiredPayments = Payment::where('status', 'pending')
                ->whereNotNull('countdown_seconds')
                ->get()
                ->filter(function ($payment) {
                    return $payment->isExpired();
                });

            if ($expiredPayments->isEmpty()) {
                $this->info('No expired payments found.');
                return Command::SUCCESS;
            }

            $this->info("Found {$expiredPayments->count()} expired payment(s).");

            foreach ($expiredPayments as $payment) {
                try {
                    // Mark as failed - ini akan trigger PaymentObserver
                    $payment->markAsFailed();
                    
                    // Update booking status juga ke 'cancelled' agar consistency
                    if ($payment->booking->status === 'pending') {
                        $payment->booking->update(['status' => 'cancelled']);
                    }

                    $this->info("Payment #{$payment->id} marked as failed. Seats returned to available.");
                    Log::info("Payment #{$payment->id} auto-expired and marked as failed.");
                } catch (\Exception $e) {
                    $this->error("Error processing payment #{$payment->id}: " . $e->getMessage());
                    Log::error("Error expiring payment #{$payment->id}: " . $e->getMessage());
                }
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            Log::error('Error in ExpirePayments command: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
