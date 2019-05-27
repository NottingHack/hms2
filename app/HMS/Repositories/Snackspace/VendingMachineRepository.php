<?php

namespace HMS\Repositories\Snackspace;

use HMS\Entities\Snackspace\VendingMachine;

interface VendingMachineRepository
{
    /**
     * Save VendingMachine to the DB.
     *
     * @param VendingMachine $vendingMachine
     */
    public function save(VendingMachine $vendingMachine);
}
