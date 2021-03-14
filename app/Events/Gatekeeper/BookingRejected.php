<?php

namespace App\Events\Gatekeeper;

use HMS\Entities\Gatekeeper\TemporaryAccessBooking;
use HMS\Entities\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingRejected implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var TemporaryAccessBooking
     */
    public $booking;

    /**
     * @var string
     */
    public $reason;

    /**
     * @var User
     */
    public $rejectedBy;

    /**
     * @var int
     */
    public $buildingId;

    /**
     * Create a new event instance.
     *
     * @param TemporaryAccessBooking $booking
     * @param string $reason
     * @param User $rejectedBy
     *
     * @return void
     */
    public function __construct(TemporaryAccessBooking $booking, string $reason, User $rejectedBy)
    {
        $this->booking = $booking;
        $this->reason = $reason;
        $this->rejectedBy = $rejectedBy;
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
            'bookingId' => $this->booking->getId(),
        ];
    }
}
