<?php

namespace HMS\Repositories\Banking;

use HMS\Entities\User;
use HMS\Entities\Banking\MembershipStatusNotification;

interface MembershipStatusNotificationRepository
{
    /**
     * Find outstanding notifications.
     *
     * @return MembershipStatusNotification[]
     */
    public function findOutstandingNotifications();

    /**
     * Find outstanding notifications for a given user.
     *
     * @param User $user
     *
     * @return MembershipStatusNotification[]
     */
    public function findOutstandingNotificationsByUser(User $user);

    /**
     * Find by user.
     *
     * @param User $user
     *
     * @return MembershipStatusNotification[]
     */
    public function findByUser(User $user);

    /**
     * Save MembershipStatusNotification to the DB.
     *
     * @param MembershipStatusNotification $membershipStatusNotification
     */
    public function save(MembershipStatusNotification $membershipStatusNotification);
}
