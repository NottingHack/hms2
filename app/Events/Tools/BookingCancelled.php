<?php

namespace App\Events\Tools;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class BookingCancelled
{
    use Dispatchable, SerializesModels;

    /**
     * @var int
     */
    protected $bookingId;

    /**
     * Create a new event instance.
     *
     * @param int $bookingId id of the cancelled booking
     *
     * @return void
     */
    public function __construct(int $bookingId)
    {
        $this->bookingId = $bookingId;
    }
}
