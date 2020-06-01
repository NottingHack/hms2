<?php

namespace HMS\Repositories\Gatekeeper\Doctrine;

use Doctrine\ORM\EntityRepository;
use HMS\Entities\Gatekeeper\ZoneOccupant;
use HMS\Repositories\Gatekeeper\ZoneOccupantRepository;

class DoctrineZoneOccupantRepository extends EntityRepository implements ZoneOccupantRepository
{
    /**
     * Save ZoneOccupant to the DB.
     *
     * @param ZoneOccupant $zoneOccupant
     */
    public function save(ZoneOccupant $zoneOccupant)
    {
        $this->_em->persist($zoneOccupant);
        $this->_em->flush();
    }
}