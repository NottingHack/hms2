<?php

namespace HMS\Repositories;

use Carbon\Carbon;

interface InviteRepository
{
    /**
     * Create a new invite if not found.
     *
     * @param  string $email
     * @return Invite
     */
    public function findOrCreateByEmail($email);

    /**
     * remove all invites older than a given date.
     * @param  Carbon $date
     * @return array
     */
    public function removeAllOlderThan(Carbon $date);
}
