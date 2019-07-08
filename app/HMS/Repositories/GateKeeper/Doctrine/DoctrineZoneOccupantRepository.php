<?php

namespace HMS\Repositories\GateKeeper\Doctrine;

use HMS\Entities\GateKeeper\ZoneOccupant;
use Doctrine\ORM\EntityRepository;
use HMS\Repositories\GateKeeper\ZoneOccupantRepository;

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
