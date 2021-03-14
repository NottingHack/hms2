<?php

namespace App\Events\Gatekeeper;

use App\Http\Resources\Gatekeeper\TemporaryAccessBooking as TemporaryAccessBookingResources;
use HMS\Entities\Gatekeeper\TemporaryAccessBooking;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingApproved implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var TemporaryAccessBooking
     */
    public $booking;

    /**
     * @var int
     */
    public $buildingId;

    /**
     * Create a new event instance.
     *
     * @param TemporaryAccessBooking $booking
     *
     * @return void
     */
    public function __construct(TemporaryAccessBooking $booking)
    {
        $this->booking = $booking;
        $this->buildingId = $booking->getBookableArea()->getBuilding()->getId();
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
            'booking' => (new TemporaryAccessBookingResources($this->booking))->resolve(),
        ];
    }
}
