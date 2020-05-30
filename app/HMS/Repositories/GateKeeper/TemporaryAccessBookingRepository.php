<?php

namespace HMS\Repositories\GateKeeper;

use Carbon\Carbon;
use HMS\Entities\User;
use HMS\Entities\GateKeeper\Building;
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
     * Check for any Bookings that would clash with a given start and end time.
     *
     * @param User $user
     * @param Building $building
     * @param Carbon $start
     * @param Carbon $end
     *
     * @return TemporaryAccessBooking[]
     */
    public function checkForClashByUserForBuilding(User $user, Building $building, Carbon $start, Carbon $end);

    /**
     * Get any current bookings.
     *
     * @return TemporaryAccessBooking[]
     */
    public function findCurrent();

    /**
     * Count future bookings for a User on a given Building.
     *
     * @param Building $building
     * @param User $user
     *
     * @return int
     */
    public function countFutureByBuildingAndUser(Building $building, User $user): int;

    /**
     * Count future bookings for a User grouped by Building id's.
     *
     * @param User $user
     *
     * @return array
     */
    public function countFutureForUserByBuildings(User $user);

    /**
     * @param Carbon $start
     * @param Carbon $end
     *
     * @return TemporaryAccessBooking[]
     */
    public function findBetween(Carbon $start, Carbon $end);

    /**
     * @param Carbon $start
     * @param Carbon $end
     * @param Building $building
     *
     * @return TemporaryAccessBooking[]
     */
    public function findBetweenForBuilding(Carbon $start, Carbon $end, Building $building);

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
