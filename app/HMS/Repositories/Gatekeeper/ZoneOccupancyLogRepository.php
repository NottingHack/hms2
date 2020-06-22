<?php

namespace HMS\Repositories\Gatekeeper;

use HMS\Entities\Gatekeeper\ZoneOccupancyLog;

interface ZoneOccupancyLogRepository
{
    /**
     * Save ZoneOccupancyLog to the DB.
     *
     * @param ZoneOccupancyLog $zoneOccupancyLog
     */
    public function save(ZoneOccupancyLog $zoneOccupancyLog);
}
