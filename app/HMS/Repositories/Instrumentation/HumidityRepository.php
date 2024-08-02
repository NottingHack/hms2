<?php

namespace HMS\Repositories\Instrumentation;

use HMS\Entities\Instrumentation\Humidity;

interface HumidityRepository
{
    /**
     * Finds all entities in the repository.
     *
     * @return Humidity[]
     */
    public function findAll();

    /**
     * Save Humidity to the DB.
     *
     * @param Humidity $humidity
     */
    public function save(Humidity $humidity);
}
