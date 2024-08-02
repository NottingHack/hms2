<?php

namespace HMS\Repositories\Instrumentation\Doctrine;

use Doctrine\ORM\EntityRepository;
use HMS\Entities\Instrumentation\LightLevel;
use HMS\Repositories\Instrumentation\LightLevelRepository;

class DoctrineLightLevelRepository extends EntityRepository implements LightLevelRepository
{
    /**
     * Finds all entities in the repository.
     *
     * @return LightLevel[]
     */
    public function findAll()
    {
        return parent::findAll();
    }

    /**
     * Save LightLevel to the DB.
     *
     * @param LightLevel $lightLevel
     */
    public function save(LightLevel $lightLevel)
    {
        $this->_em->persist($lightLevel);
        $this->_em->flush();
    }
}
