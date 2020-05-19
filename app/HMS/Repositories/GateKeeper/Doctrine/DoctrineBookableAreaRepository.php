<?php

namespace HMS\Repositories\GateKeeper\Doctrine;

use Doctrine\ORM\EntityRepository;
use HMS\Entities\GateKeeper\Building;
use HMS\Entities\GateKeeper\BookableArea;
use HMS\Repositories\GateKeeper\BookableAreaRepository;
use LaravelDoctrine\ORM\Pagination\PaginatesFromRequest;

class DoctrineBookableAreaRepository extends EntityRepository implements BookableAreaRepository
{
    use PaginatesFromRequest;

    /**
     * Finds all entities in the repository.
     *
     * @return array The entities.
     */
    public function findAll()
    {
        return parent::findAll();
    }

    /**
     * @param Building $building
     * @param int $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateByBuilding(Building $building, $perPage = 15, $pageName = 'page')
    {
        $q = parent::createQueryBuilder('bookableArea')
            ->where('bookableArea.building = :building_id');

        $q = $q->setParameter('building_id', $building->getId())->getQuery();

        return $this->paginate($q, $perPage, $pageName);
    }

    /**
     * Save BookableArea to the DB.
     *
     * @param BookableArea $bookableArea
     */
    public function save(BookableArea $bookableArea)
    {
        $this->_em->persist($bookableArea);
        $this->_em->flush();
    }

    /**
     * Remove a BookableArea from the DB.
     *
     * @param BookableArea $bookableArea
     */
    public function remove(BookableArea $bookableArea)
    {
        $this->_em->remove($bookableArea);
        $this->_em->flush();
    }
}
