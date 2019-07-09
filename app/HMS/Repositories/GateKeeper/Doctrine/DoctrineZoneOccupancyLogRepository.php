<?php

namespace HMS\Repositories\GateKeeper\Doctrine;

use Doctrine\ORM\EntityRepository;
use HMS\Entities\GateKeeper\ZoneOccupancyLog;
use HMS\Repositories\GateKeeper\ZoneOccupancyLogRepository;

class DoctrineZoneOccupancyLogRepository extends EntityRepository implements ZoneOccupancyLogRepository
{
    /**
     * Save ZoneOccupancyLog to the DB.
     *
     * @param ZoneOccupancyLog $zoneOccupancyLog
     */
    public function save(ZoneOccupancyLog $zoneOccupancyLog)
    {
        $this->_em->persist($zoneOccupancyLog);
        $this->_em->flush();
    }
}
