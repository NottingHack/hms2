<?php

namespace HMS\Repositories\Instrumentation\Doctrine;

use Doctrine\ORM\EntityRepository;
use HMS\Entities\Instrumentation\Humidity;
use HMS\Repositories\Instrumentation\HumidityRepository;

class DoctrineHumidityRepository extends EntityRepository implements HumidityRepository
{
    /**
     * Finds all entities in the repository.
     *
     * @return Humidity[]
     */
    public function findAll()
    {
        return parent::findAll();
    }

    /**
     * Save Humidity to the DB.
     *
     * @param Humidity $humidity
     */
    public function save(Humidity $humidity)
    {
        $this->_em->persist($humidity);
        $this->_em->flush();
    }
}
