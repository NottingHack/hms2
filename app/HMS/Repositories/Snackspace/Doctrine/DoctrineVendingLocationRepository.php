<?php

namespace HMS\Repositories\Snackspace\Doctrine;

use Doctrine\ORM\EntityRepository;
use HMS\Entities\Snackspace\VendingMachine;
use HMS\Entities\Snackspace\VendingLocation;
use HMS\Repositories\Snackspace\VendingLocationRepository;

class DoctrineVendingLocationRepository extends EntityRepository implements VendingLocationRepository
{
    /**
     * Find a VendingLocation.
     *
     * @param $id
     *
     * @return null|VendingLocation
     */
    public function findOneById($id)
    {
        return parent::findOneById($id);
    }

    /**
     * Find all locations for a given VendingMachine
     *
     * @param VendingMachine $vendingMachine
     *
     * @return VendingLocation[]
     */
    public function findByVendingMachine(VendingMachine $vendingMachine)
    {
        return parent::findByVendingMachine($vendingMachine);
    }

    /**
     * Save VendingLocation to the DB.
     *
     * @param VendingLocation $vendingLocation
     */
    public function save(VendingLocation $vendingLocation)
    {
        $this->_em->persist($vendingLocation);
        $this->_em->flush();
    }
}
