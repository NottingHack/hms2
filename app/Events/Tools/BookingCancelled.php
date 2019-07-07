<?php

namespace App\Events\Tools;

use HMS\Entities\Tools\Tool;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class BookingCancelled
{
    use Dispatchable, SerializesModels;

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
     * @param int $bookingId id of the cancelled booking
     *
     * @return void
     */
    public function __construct(Tool $tool, int $bookingId)
    {
        $this->tool = $tool;
        $this->bookingId = $bookingId;
    }
}
