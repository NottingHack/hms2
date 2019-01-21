<?php

namespace HMS\Repositories;

use Carbon\Carbon;
use HMS\Entities\Invite;

interface InviteRepository
{
    /**
     * Create a new invite if not found.
     *
     * @param string $email
     *
     * @return Invite
     */
    public function findOrCreateByEmail($email);

    /**
     * Find an invite by emial.
     *
     * @param string $email
     *
     * @return null|Invite
     */
    public function findOneByEmail($email);

    /**
     * Find an invite by token.
     *
     * @param string $token
     *
     * @return null|Invite
     */
    public function findOneByInviteToken($token);

    /**
     * Remove all invites older than a given date.
     *
     * @param Carbon $date
     *
     * @return array
     */
    public function removeAllOlderThan(Carbon $date);

    /**
     * Remove a single invites.
     *
     * @param Invite $invite
     */
    public function remove(Invite $invite);
}
