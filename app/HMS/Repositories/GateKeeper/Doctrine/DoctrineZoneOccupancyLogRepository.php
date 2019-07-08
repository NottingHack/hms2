<?php

namespace HMS\Repositories\GateKeeper\Doctrine;

use HMS\Entities\GateKeeper\ZoneOccupancyLog;
use Doctrine\ORM\EntityRepository;
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
