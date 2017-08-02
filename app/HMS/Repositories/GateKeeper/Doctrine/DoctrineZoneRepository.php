<?php

namespace HMS\Repositories\GateKeeper\Doctrine;

use HMS\Entities\GateKeeper\Zone;
use Doctrine\ORM\EntityRepository;
use HMS\Repositories\GateKeeper\ZoneRepository;

class DoctrineZoneRepository extends EntityRepository implements ZoneRepository
{
    /**
     * save Zone to the DB. 
     * @param  Zone $zone
     */
    public function save(Zone $zone)
    {
        $this->_em->persist($zone);
        $this->_em->flush();
    }
}
