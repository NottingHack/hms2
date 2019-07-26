<?php

namespace App\Events\Tools;

use HMS\Entities\Tools\Booking;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class BookingChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Booking
     */
    public $orignalBooking;

    /**
     * @var Booking
     */
    public $booking;

    /**
     * Create a new event instance.
     *
     * @param Booking $orignalBooking
     * @param Booking $booking
     *
     * @return void
     */
    public function __construct(Booking $orignalBooking, Booking $booking)
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
        return new Channel('tools.' . $this->booking->getTool()->getId() . '.bookings');
    }
}
