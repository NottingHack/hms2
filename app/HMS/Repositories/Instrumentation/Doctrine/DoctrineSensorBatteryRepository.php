<?php

namespace HMS\Repositories\Instrumentation\Doctrine;

use Doctrine\ORM\EntityRepository;
use HMS\Entities\Instrumentation\SensorBattery;
use HMS\Repositories\Instrumentation\SensorBatteryRepository;

class DoctrineSensorBatteryRepository extends EntityRepository implements SensorBatteryRepository
{
    /**
     * Finds all entities in the repository.
     *
     * @return SensorBattery[]
     */
    public function findAll()
    {
        return parent::findAll();
    }

    /**
     * Save SensorBattery to the DB.
     *
     * @param SensorBattery $sensorBattery
     */
    public function save(SensorBattery $sensorBattery)
    {
        $this->_em->persist($sensorBattery);
        $this->_em->flush();
    }
}
