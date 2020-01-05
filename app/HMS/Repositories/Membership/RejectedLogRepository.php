<?php

namespace HMS\Repositories\Membership;

use HMS\Entities\User;
use HMS\Entities\Membership\RejectedLog;

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
