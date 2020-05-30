<?php

namespace HMS\Factories\GateKeeper;

use Carbon\Carbon;
use HMS\Entities\User;
use HMS\Entities\GateKeeper\BookableArea;
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
     * @param BookableArea|null $bookableArea
     * @param string|null $color
     * @param string|null $notes
     * @param bool $approved Default false.
     * @param User|null $approvedBy
     *
     * @return TemporaryAccessBooking
     */
    public function create(
        Carbon $start,
        Carbon $end,
        User $user,
        ?BookableArea $bookableArea = null,
        ?string $color = null,
        ?string $notes = null,
        bool $approved = false,
        ?User $approvedBy = null
    ) {
        $_temporaryAccessBooking = new TemporaryAccessBooking();
        $_temporaryAccessBooking->setStart($start);
        $_temporaryAccessBooking->setEnd($end);
        $_temporaryAccessBooking->setUser($user);
        $_temporaryAccessBooking->setBookableArea($bookableArea);
        $_temporaryAccessBooking->setColor($color);
        $_temporaryAccessBooking->setNotes($notes);
        $_temporaryAccessBooking->setApproved($approved);
        $_temporaryAccessBooking->setApprovedBy($approvedBy);

        return $_temporaryAccessBooking;
    }
}
