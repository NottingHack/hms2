<?php

namespace HMS\Repositories\Snackspace\Doctrine;

use Doctrine\ORM\EntityRepository;
use HMS\Entities\Snackspace\VendingLocation;
use HMS\Repositories\Snackspace\VendingLocationRepository;

class DoctrineVendingLocationRepository extends EntityRepository implements VendingLocationRepository
{
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
