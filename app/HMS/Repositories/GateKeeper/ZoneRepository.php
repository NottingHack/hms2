<?php

namespace HMS\Repositories\GateKeeper;

use HMS\Entities\GateKeeper\Zone;

interface ZoneRepository
{
    /**
     * Save Zone to the DB.
     *
     * @param Zone $zone
     */
    public function save(Zone $zone);
}
