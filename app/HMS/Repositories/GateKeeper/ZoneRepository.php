<?php

namespace HMS\Repositories\GateKeeper;

use HMS\Entities\GateKeeper\Zone;

interface ZoneRepository
{
    /**
     * Find all zones.
     *
     * @return Zone[]
     */
    public function findAll();

    /**
     * Find one zone by short name.
     *
     * @param string $shortName
     *
     * @return Zone
     */
    public function findOneByShortName(string $shortName);

    /**
     * Save Zone to the DB.
     *
     * @param Zone $zone
     */
    public function save(Zone $zone);
}
