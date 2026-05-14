<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

//  "implements ShouldBroadcastNow" ini wajib ada agar langsung dikirim ke WebSockets!
class SeatStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $seatId;
    public $status;

    /**
     * Create a new event instance.
     */
    public function __construct($seatId, $status)
    {
        $this->seatId = $seatId;
        $this->status = $status;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        // Mengirim data ke channel publik bernama 'cinema-seats'
        return [
            new Channel('cinema-seats'),
        ];
    }

    /**
     *  Custom nama event agar mudah ditangkap di frontend
     */
    public function broadcastAs(): string
    {
        return 'seat.updated';
    }
}
