<?php

namespace HMS\Repositories\Snackspace;

use HMS\Entities\Snackspace\VendingLocation;

interface VendingLocationRepository
{
    /**
     * Save VendingLocation to the DB.
     *
     * @param VendingLocation $vendingLocation
     */
    public function save(VendingLocation $vendingLocation);
}
