<?php

namespace HMS\Repositories\GateKeeper;

use HMS\Entities\GateKeeper\ZoneOccupant;

interface ZoneOccupantRepository
{
    /**
     * Save ZoneOccupant to the DB.
     *
     * @param ZoneOccupant $zoneOccupant
     */
    public function save(ZoneOccupant $zoneOccupant);
}
