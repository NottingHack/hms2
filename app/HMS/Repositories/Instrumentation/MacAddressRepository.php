<?php

namespace HMS\Repositories\Instrumentation;

use HMS\Entities\Instrumentation\MacAddress;

interface MacAddressRepository
{
    /**
     * Count of MacAddresses seen in the last 5 minutes.
     *
     * @param bool $filterIgnores Default true
     *
     * @return int
     */
    public function countSeenLastFiveMinutes(bool $filterIgnores = true): int;

    /**
     * Save MacAddress to the DB.
     *
     * @param MacAddress $macAddress
     */
    public function save(MacAddress $macAddress);
}
