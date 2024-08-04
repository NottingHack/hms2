<?php

namespace HMS\Repositories\Gatekeeper;

use HMS\Entities\Gatekeeper\Door;

interface DoorRepository
{
    /**
     * Finds all entities in the repository.
     *
     * @return array The entities.
     */
    public function findAll();

    /**
     * Find by short name.
     *
     * @param string $shortName
     *
     * @return null|Door
     */
    public function findOneByShortName(string $shortName);

    /**
     * Save Door to the DB.
     *
     * @param Door $door
     */
    public function save(Door $door);
}
