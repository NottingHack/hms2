<?php

namespace HMS\Repositories\Instrumentation\Doctrine;

use Doctrine\ORM\EntityRepository;
use HMS\Entities\Instrumentation\Temperature;
use HMS\Repositories\Instrumentation\TemperatureRepository;

class DoctrineTemperatureRepository extends EntityRepository implements TemperatureRepository
{
    /**
     * Finds all entities in the repository.
     *
     * @return Temperature[]
     */
    public function findAll()
    {
        return parent::findAll();
    }

    /**
     * Save Temperature to the DB.
     *
     * @param Temperature $temperature
     */
    public function save(Temperature $temperature)
    {
        $this->_em->persist($temperature);
        $this->_em->flush();
    }
}
