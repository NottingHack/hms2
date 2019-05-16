<?php

namespace App\Events\Tools;

use HMS\Entities\Tools\Booking;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class NewBooking
{
    use Dispatchable, SerializesModels;

    /**
     * @var Booking
     */
    public $booking;

    /**
     * Create a new event instance.
     *
     * @param Booking $booking
     *
     * @return void
     */
    public function __construct(Booking $booking)
    {
        //
        $this->booking = $booking;
    }
}
