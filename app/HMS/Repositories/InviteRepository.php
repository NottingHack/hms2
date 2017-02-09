<?php

namespace HMS\Repositories;

use Carbon\Carbon;
use HMS\Entities\Invite;

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
     * find an invite by emial.
     *
     * @param  string $email
     * @return Invite
     */
    public function findOneByEmail($email);

    /**
     * remove all invites older than a given date.
     * @param  Carbon $date
     * @return array
     */
    public function removeAllOlderThan(Carbon $date);

    /**
     * remove a single invites.
     * @param  Invite $invite
     */
    public function remove(Invite $invite);
}
