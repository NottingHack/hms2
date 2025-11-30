<?php

namespace HMS\Repositories\Gatekeeper\Doctrine;

use Doctrine\ORM\EntityRepository;
use HMS\Entities\Gatekeeper\BookableArea;
use HMS\Entities\Gatekeeper\Building;
use HMS\Repositories\Gatekeeper\BookableAreaRepository;
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
     * @param int $id
     *
     * @return null|BookableArea
     */
    public function findOneById(int $id)
    {
        return parent::findOneById($id);
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
