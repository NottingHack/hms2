<?php

namespace HMS\Repositories\Instrumentation\Doctrine;

use Doctrine\ORM\EntityRepository;
use HMS\Entities\Instrumentation\ElectricMeter;
use HMS\Repositories\Instrumentation\ElectricMeterRepository;

class DoctrineElectricMeterRepository extends EntityRepository implements ElectricMeterRepository
{
    /**
     * Finds all entities in the repository.
     *
     * @return ElectricMeter[]
     */
    public function findAll()
    {
        return parent::findAll();
    }

    /**
     * Save ElectricMeter to the DB.
     *
     * @param ElectricMeter $electricMeter
     */
    public function save(ElectricMeter $electricMeter)
    {
        $this->_em->persist($electricMeter);
        $this->_em->flush();
    }
}
