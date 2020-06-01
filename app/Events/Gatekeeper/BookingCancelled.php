<?php

namespace App\Events\Gatekeeper;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class BookingCancelled implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var int
     */
    public $bookingId;

    /**
     * @var int
     */
    protected $buildingId;

    /**
     * Create a new event instance.
     *
     * @param int $bookingId id of the cancelled booking
     *
     * @return void
     */
    public function __construct(int $bookingId, int $buildingId)
    {
        $this->bookingId = $bookingId;
        $this->buildingId = $buildingId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('gatekeeper.temporaryAccessBookings.' . $this->buildingId);
    }

    /**
     * Get the data that should be sent with the broadcasted event.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'bookingId' => $this->bookingId,
        ];
    }
}
