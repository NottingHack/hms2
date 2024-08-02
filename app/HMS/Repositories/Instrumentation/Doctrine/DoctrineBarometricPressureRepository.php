<?php

namespace HMS\Repositories\Instrumentation\Doctrine;

use Doctrine\ORM\EntityRepository;
use HMS\Entities\Instrumentation\BarometricPressure;
use HMS\Repositories\Instrumentation\BarometricPressureRepository;

class DoctrineBarometricPressureRepository extends EntityRepository implements BarometricPressureRepository
{
    /**
     * Finds all entities in the repository.
     *
     * @return BarometricPressure[]
     */
    public function findAll()
    {
        return parent::findAll();
    }

    /**
     * Save BarometricPressure to the DB.
     *
     * @param BarometricPressure $barometricPressure
     */
    public function save(BarometricPressure $barometricPressure)
    {
        $this->_em->persist($barometricPressure);
        $this->_em->flush();
    }
}
