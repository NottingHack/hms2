<?php

namespace HMS\Repositories\Snackspace;

use HMS\Entities\Snackspace\VendingMachine;

interface VendingMachineRepository
{
    /**
     * Finds all entities in the repository.
     *
     * @return VendingMachine[]
     */
    public function findAll();

    /**
     * Finds entities in the repository by VendingMachineType.
     *
     * @return VendingMachine[]
     */
    public function findByType($type);

    /**
     * @param int $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateAll($perPage = 15, $pageName = 'page');

    /**
     * Save VendingMachine to the DB.
     *
     * @param VendingMachine $vendingMachine
     */
    public function save(VendingMachine $vendingMachine);
}
