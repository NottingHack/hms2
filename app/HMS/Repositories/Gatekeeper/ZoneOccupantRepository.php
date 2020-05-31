<?php

namespace HMS\Repositories\Gatekeeper;

use HMS\Entities\Gatekeeper\ZoneOccupant;

interface ZoneOccupantRepository
{
    /**
     * Save ZoneOccupant to the DB.
     *
     * @param ZoneOccupant $zoneOccupant
     */
    public function save(ZoneOccupant $zoneOccupant);
}
