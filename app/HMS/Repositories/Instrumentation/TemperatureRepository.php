<?php

namespace HMS\Repositories\Instrumentation;

use HMS\Entities\Instrumentation\Temperature;

interface TemperatureRepository
{
    /**
     * Finds all entities in the repository.
     *
     * @return Temperature[]
     */
    public function findAll();

    /**
     * Save Temperature to the DB.
     *
     * @param Temperature $temperature
     */
    public function save(Temperature $temperature);
}
