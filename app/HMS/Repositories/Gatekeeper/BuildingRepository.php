<?php

namespace HMS\Repositories\Gatekeeper;

use HMS\Entities\Gatekeeper\Building;

interface BuildingRepository
{
    /**
     * @param int $id
     *
     * @return null|Building
     */
    public function findOneById(int $id);

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
