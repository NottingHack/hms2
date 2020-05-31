<?php

namespace HMS\Repositories\Gatekeeper\Doctrine;

use Doctrine\ORM\EntityRepository;
use HMS\Entities\Gatekeeper\Building;
use HMS\Repositories\Gatekeeper\BuildingRepository;
use LaravelDoctrine\ORM\Pagination\PaginatesFromRequest;

class DoctrineBuildingRepository extends EntityRepository implements BuildingRepository
{
    use PaginatesFromRequest;

    /**
     * Find all buildings.
     *
     * @return Building[]
     */
    public function findAll()
    {
        return parent::findAll();
    }

    /**
     * Save Building to the DB.
     *
     * @param Building $building
     */
    public function save(Building $building)
    {
        $this->_em->persist($building);
        $this->_em->flush();
    }
}
