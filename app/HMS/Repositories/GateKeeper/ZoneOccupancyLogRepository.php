<?php

namespace HMS\Repositories\GateKeeper;

use HMS\Entities\GateKeeper\ZoneOccupancyLog;

interface ZoneOccupancyLogRepository
{
    /**
     * Save ZoneOccupancyLog to the DB.
     *
     * @param ZoneOccupancyLog $zoneOccupancyLog
     */
    public function save(ZoneOccupancyLog $zoneOccupancyLog);
}
