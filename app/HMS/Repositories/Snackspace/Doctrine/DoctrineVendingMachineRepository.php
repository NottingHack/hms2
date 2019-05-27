<?php

namespace HMS\Repositories\Snackspace\Doctrine;

use Doctrine\ORM\EntityRepository;
use HMS\Entities\Snackspace\VendingMachine;
use HMS\Repositories\Snackspace\VendingMachineRepository;

class DoctrineVendingMachineRepository extends EntityRepository implements VendingMachineRepository
{
    /**
     * Save VendingMachine to the DB.
     *
     * @param VendingMachine $vendingMachine
     */
    public function save(VendingMachine $vendingMachine)
    {
        $this->_em->persist($vendingMachine);
        $this->_em->flush();
    }
}
