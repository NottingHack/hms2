<?php

namespace App\Events\Tools;

use HMS\Entities\Tools\Tool;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class BookingCancelled implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Tool
     */
    public $tool;

    /**
     * @var int
     */
    public $bookingId;

    /**
     * Create a new event instance.
     *
     * @param Tool $tool Tool to which this booking belonged
     * @param int $bookingId id of the cancelled booking
     *
     * @return void
     */
    public function __construct(Tool $tool, int $bookingId)
    {
        $this->tool = $tool;
        $this->bookingId = $bookingId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('tools.' . $this->tool->getId() . '.bookings');
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
            'toolId' => $this->tool->getId(),
        ];
    }
}
