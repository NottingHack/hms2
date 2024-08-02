<?php

namespace HMS\Repositories\Instrumentation;

use HMS\Entities\Instrumentation\BarometricPressure;

interface BarometricPressureRepository
{
    /**
     * Finds all entities in the repository.
     *
     * @return BarometricPressure[]
     */
    public function findAll();

    /**
     * Save BarometricPressure to the DB.
     *
     * @param BarometricPressure $barometricPressure
     */
    public function save(BarometricPressure $barometricPressure);
}
