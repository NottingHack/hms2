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
     * Finds one entitiy in the repository.
     *
     * @param int $id
     *
     * @return ElectricMeter|null
     */
    public function findOneById(int $id);

    /**
     * Save ElectricMeter to the DB.
     *
     * @param ElectricMeter $electricMeter
     */
    public function save(ElectricMeter $electricMeter);
}
