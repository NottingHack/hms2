<?php

namespace App\Events\GateKeeper;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use HMS\Entities\GateKeeper\TemporaryAccessBooking;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Http\Resources\GateKeeper\TemporaryAccessBooking as TemporaryAccessBookingResources;

class BookingChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var TemporaryAccessBooking
     */
    public $orignalBooking;

    /**
     * @var TemporaryAccessBooking
     */
    public $booking;

    /**
     * Create a new event instance.
     *
     * @param TemporaryAccessBooking $orignalBooking
     * @param TemporaryAccessBooking $booking
     *
     * @return void
     */
    public function __construct(TemporaryAccessBooking $orignalBooking, TemporaryAccessBooking $booking)
    {
        $this->orignalBooking = $orignalBooking;
        $this->booking = $booking;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('gatekeeper.temporaryAccessBookings');
    }

    /**
     * Get the data that should be sent with the broadcasted event.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'orignalBooking' =>(new TemporaryAccessBookingResources($response))->resolve(),
            'booking' => (new TemporaryAccessBookingResources($response))->resolve(),
        ];
    }
}
