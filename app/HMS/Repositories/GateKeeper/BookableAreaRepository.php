<?php

namespace HMS\Repositories\GateKeeper;

use HMS\Entities\GateKeeper\Building;
use HMS\Entities\GateKeeper\BookableArea;

interface BookableAreaRepository
{
    /**
     * Finds all entities in the repository.
     *
     * @return array The entities.
     */
    public function findAll();

    /**
     * @param Building $building
     * @param int $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateByBuilding(Building $building, $perPage = 15, $pageName = 'page');

    /**
     * Save BookableArea to the DB.
     *
     * @param BookableArea $bookableArea
     */
    public function save(BookableArea $bookableArea);

    /**
     * Remove a BookableArea from the DB.
     *
     * @param BookableArea $bookableArea
     */
    public function remove(BookableArea $bookableArea);
}
