<?php

namespace HMS\Repositories\Instrumentation;

use HMS\Entities\Instrumentation\ElectricReading;

interface ElectricReadingRepository
{
    /**
     * Finds all entities in the repository.
     *
     * @return ElectricReading[]
     */
    public function findAll();

    /**
     * Save ElectricReading to the DB.
     *
     * @param ElectricReading $electricReading
     */
    public function save(ElectricReading $electricReading);
}
