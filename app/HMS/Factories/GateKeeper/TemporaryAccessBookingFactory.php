<?php

namespace HMS\Factories\GateKeeper;

use Carbon\Carbon;
use HMS\Entities\User;
use HMS\Entities\GateKeeper\TemporaryAccessBooking;
use HMS\Repositories\GateKeeper\TemporaryAccessBookingRepository;

class TemporaryAccessBookingFactory
{
    /**
     * @var TemporaryAccessBookingRepository
     */
    protected $temporaryAccessBookingRepository;

    /**
     * @param TemporaryAccessBookingRepository $temporaryAccessBookingRepository
     */
    public function __construct(TemporaryAccessBookingRepository $temporaryAccessBookingRepository)
    {
        $this->temporaryAccessBookingRepository = $temporaryAccessBookingRepository;
    }

    /**
     * Function to instantiate a new TemporaryAccessBooking from given params.
     *
     * @param Carbon $start
     * @param Carbon $end
     * @param User $user
     *
     * @return TemporaryAccessBooking
     */
    public function create(Carbon $start, Carbon $end, User $user)
    {
        $_temporaryAccessBooking = new TemporaryAccessBooking();
        $_temporaryAccessBooking->setStart($start);
        $_temporaryAccessBooking->setEnd($end);
        $_temporaryAccessBooking->setUser($user);

        return $_temporaryAccessBooking;
    }
}
