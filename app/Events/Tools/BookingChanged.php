<?php

namespace App\Events\Tools;

use HMS\Entities\Tools\Booking;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class BookingChanged
{
    use Dispatchable, SerializesModels;

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
}
