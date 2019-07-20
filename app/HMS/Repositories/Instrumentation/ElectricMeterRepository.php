<?php

namespace HMS\Repositories\Instrumentation;

use HMS\Entities\Instrumentation\ElectricMeter;

interface ElectricMeterRepository
{
    /**
     * Finds all entities in the repository.
     *
     * @return ElectricMeter[]
     */
    public function findAll();

    /**
     * Save ElectricMeter to the DB.
     *
     * @param ElectricMeter $electricMeter
     */
    public function save(ElectricMeter $electricMeter);
}
