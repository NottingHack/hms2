<?php

namespace HMS\Repositories\Membership;

use HMS\Entities\Membership\RejectedLog;
use HMS\Entities\User;

interface RejectedLogRepository
{
    /**
     * @param User $user
     *
     * @return RejectedLog[]
     */
    public function findByUser(User $user);

    /**
     * Save RejectedLog to the DB.
     *
     * @param RejectedLog $rejectedLog
     */
    public function save(RejectedLog $rejectedLog);
}
