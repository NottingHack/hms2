<?php

namespace HMS\Repositories\Instrumentation;

use HMS\Entities\Instrumentation\SensorBattery;

interface SensorBatteryRepository
{
    /**
     * Finds all entities in the repository.
     *
     * @return SensorBattery[]
     */
    public function findAll();

    /**
     * Save SensorBattery to the DB.
     *
     * @param SensorBattery $sensorBattery
     */
    public function save(SensorBattery $sensorBattery);
}
