<?php

namespace HMS\Repositories\Instrumentation\Doctrine;

use Doctrine\ORM\EntityRepository;
use HMS\Entities\Instrumentation\ElectricReading;
use HMS\Repositories\Instrumentation\ElectricReadingRepository;

class DoctrineElectricReadingRepository extends EntityRepository implements ElectricReadingRepository
{
    /**
     * Finds all entities in the repository.
     *
     * @return ElectricReading[]
     */
    public function findAll()
    {
        return parent::findAll();
    }

    /**
     * Save ElectricReading to the DB.
     *
     * @param ElectricReading $electricReading
     */
    public function save(ElectricReading $electricReading)
    {
        $this->_em->persist($electricReading);
        $this->_em->flush();
    }
}
