<?php

namespace HMS\Factories\Gatekeeper;

use Carbon\Carbon;
use HMS\Entities\Gatekeeper\BookableArea;
use HMS\Entities\Gatekeeper\TemporaryAccessBooking;
use HMS\Entities\User;
use HMS\Repositories\Gatekeeper\TemporaryAccessBookingRepository;

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
     * @param int $guests
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
        int $guests = 0,
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
        $_temporaryAccessBooking->setGuests($guests);
        $_temporaryAccessBooking->setColor($color);
        $_temporaryAccessBooking->setNotes($notes);
        $_temporaryAccessBooking->setApproved($approved);
        $_temporaryAccessBooking->setApprovedBy($approvedBy);

        return $_temporaryAccessBooking;
    }
}
