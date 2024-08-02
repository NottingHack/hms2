<?php

namespace HMS\Repositories\Instrumentation;

use HMS\Entities\Instrumentation\LightLevel;

interface LightLevelRepository
{
    /**
     * Finds all entities in the repository.
     *
     * @return LightLevel[]
     */
    public function findAll();

    /**
     * Save LightLevel to the DB.
     *
     * @param LightLevel $lightLevel
     */
    public function save(LightLevel $lightLevel);
}
