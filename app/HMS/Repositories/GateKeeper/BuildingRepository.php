<?php

namespace HMS\Repositories\GateKeeper;

use HMS\Entities\GateKeeper\Building;

interface BuildingRepository
{
    /**
     * Find all buildings.
     *
     * @return Building[]
     */
    public function findAll();

    /**
     * Save Building to the DB.
     *
     * @param Building $building
     */
    public function save(Building $building);

    /**
     * @param int $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateAll($perPage = 15, $pageName = 'page');
}
