<?php

namespace HMS\Repositories\GateKeeper;

use HMS\Entities\GateKeeper\Door;

interface DoorRepository
{
    /**
     * find by short name.
     * @param  string $shortName
     * @return null|Door
     */
    public function findOneByShortName(string $shortName);

    /**
     * save Door to the DB.
     * @param  Door $door
     */
    public function save(Door $door);
}
