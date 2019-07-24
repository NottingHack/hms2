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
     * Finds one entitiy in the repository.
     *
     * @param int $id
     *
     * @return ElectricMeter|null
     */
    public function findOneById(int $id)
    {
        return parent::findOneById($id);
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
