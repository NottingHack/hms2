<?php

namespace HMS\Factories\Tools;

use Carbon\Carbon;
use HMS\Entities\User;
use HMS\Entities\Tools\Tool;
use HMS\Entities\Tools\Booking;
use HMS\Repositories\Tools\BookingRepository;

class BookingFactory
{
    /**
     * @var BookingRepository
     */
    protected $bookingRepository;

    /**
     * @param BookingRepository $bookingRepository
     */
    public function __construct(BookingRepository $bookingRepository)
    {
        $this->bookingRepository = $bookingRepository;
    }

    /**
     * Function to instantiate a new Booking from given params.
     *
     * @param Carbon $start Start time
     * @param Carbon $end   End time
     * @param string $type  Normal, induction, maintenance
     * @param User $user  Who
     * @param Tool $tool  Which tool
     *
     * @return Booking
     */
    public function create(Carbon $start, Carbon $end, string $type, User $user, Tool $tool)
    {
        $_booking = new Booking();
        $_booking->setStart($start);
        $_booking->setEnd($end);
        $_booking->setType($type);
        $_booking->setUser($user);
        $_booking->setTool($tool);

        return $_booking;
    }
}
