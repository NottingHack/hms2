<?php

namespace HMS\Repositories\Gatekeeper;

use HMS\Entities\User;
use HMS\Entities\Gatekeeper\ZoneOccupant;

interface ZoneOccupantRepository
{
    /**
     * Find One By User.
     *
     * @param User $user
     *
     * @return null|User
     */
    public function findOneByUser(User $user);

    /**
     * Save ZoneOccupant to the DB.
     *
     * @param ZoneOccupant $zoneOccupant
     */
    public function save(ZoneOccupant $zoneOccupant);
}
