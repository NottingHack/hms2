<?php

namespace HMS\Repositories\Snackspace;

use HMS\Entities\Snackspace\VendingMachine;
use HMS\Entities\Snackspace\VendingLocation;

interface VendingLocationRepository
{
    /**
     * Find a VendingLocation.
     *
     * @param int $id
     *
     * @return null|VendingLocation
     */
    public function findOneById(int $id);

    /**
     * Find all locations for a given VendingMachine.
     *
     * @param VendingMachine $vendingMachine
     *
     * @return VendingLocation[]
     */
    public function findByVendingMachine(VendingMachine $vendingMachine);

    /**
     * Save VendingLocation to the DB.
     *
     * @param VendingLocation $vendingLocation
     */
    public function save(VendingLocation $vendingLocation);
}
