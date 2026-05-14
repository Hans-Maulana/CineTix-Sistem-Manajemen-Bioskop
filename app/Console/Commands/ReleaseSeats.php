<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ReleaseSeats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:release-seats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
   public function handle()
    {
        $expiredSeats = \App\Models\Seat::where('status', 'pending')
                        ->where('locked_until', '<', now())
                        ->get();

        foreach ($expiredSeats as $seat) {
            $seat->update([
                'status' => 'available',
                'locked_until' => null,
                'locked_by_user_id' => null
            ]);

            broadcast(new \App\Events\SeatStatusUpdated($seat->id, 'available'));
        }

        $this->info('Kursi kedaluwarsa telah dilepas.');
    }
}
