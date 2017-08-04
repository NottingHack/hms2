<?php

namespace HMS\Repositories\GateKeeper;

use HMS\Entities\User;
use HMS\Entities\GateKeeper\AccessLog;

interface AccessLogRepository
{
    /**
     * @param  User $user
     * @return AccessLog[]
     */
    public function findByUser(User $user);

    /**
     * save AccessLog to the DB.
     * @param  AccessLog $accessLog
     */
    public function save(AccessLog $accessLog);
}