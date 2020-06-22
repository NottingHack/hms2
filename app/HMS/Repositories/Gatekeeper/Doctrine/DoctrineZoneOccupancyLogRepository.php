<?php

namespace HMS\Repositories\Gatekeeper\Doctrine;

use Doctrine\ORM\EntityRepository;
use HMS\Entities\Gatekeeper\ZoneOccupancyLog;
use HMS\Repositories\Gatekeeper\ZoneOccupancyLogRepository;

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
