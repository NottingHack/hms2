<?php

namespace HMS\Repositories\GateKeeper;

use Carbon\Carbon;
use HMS\Entities\User;
use HMS\Entities\GateKeeper\TemporaryAccessBooking;

interface TemporaryAccessBookingRepository
{
    /**
     * Check for any Bookings that would clash with a given start and end time.
     *
     * @param User $user
     * @param Carbon $start
     * @param Carbon $end
     *
     * @return TemporaryAccessBooking[]
     */
    public function checkForClashByUser(User $user, Carbon $start, Carbon $end);

    /**
     * Get any current bookings.
     *
     * @return TemporaryAccessBooking[]
     */
    public function findCurrent();

    /**
     * @param Carbon $start
     * @param Carbon $end
     *
     * @return TemporaryAccessBooking[]
     */
    public function findBetween(Carbon $start, Carbon $end);

    /**
     * Save TemporaryAccessBooking to the DB.
     *
     * @param TemporaryAccessBooking $temporaryAccessBooking
     */
    public function save(TemporaryAccessBooking $temporaryAccessBooking);

    /**
     * Remove a TemporaryAccessBooking.
     *
     * @param TemporaryAccessBooking $temporaryAccessBooking
     */
    public function remove(TemporaryAccessBooking $temporaryAccessBooking);
}
