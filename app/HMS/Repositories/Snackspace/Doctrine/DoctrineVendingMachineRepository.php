<?php

namespace HMS\Repositories\Snackspace\Doctrine;

use Doctrine\ORM\EntityRepository;
use HMS\Entities\Snackspace\VendingMachine;
use LaravelDoctrine\ORM\Pagination\PaginatesFromRequest;
use HMS\Repositories\Snackspace\VendingMachineRepository;

class DoctrineVendingMachineRepository extends EntityRepository implements VendingMachineRepository
{
    use PaginatesFromRequest;

    /**
     * Finds all entities in the repository.
     *
     * @return VendingMachine[]
     */
    public function findAll()
    {
        return parent::findAll();
    }

    /**
     * Finds entities in the repository by VendingMachineType.
     *
     * @return VendingMachine[]
     */
    public function findByType($type)
    {
        return parent::findByType($type);
    }

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
