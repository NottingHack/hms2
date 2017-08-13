<?php

namespace HMS\Repositories\Banking;

use HMS\Entities\User;
use HMS\Entities\Banking\MembershipStatusNotification;

interface MembershipStatusNotificationRepository
{
    /**
     * find outtanding notifications.
     * @return MembershipStatusNotification[]
     */
    public function findOutstandingNotifications();

    /**
     * find outtanding notifications for a given user.
     * @return MembershipStatusNotification[]
     */
    public function findOutstandingNotificationsByUser(User $user);

    /**
     * find by user.
     * @param  User $user
     * @return MembershipStatusNotification[]
     */
    public function findByUser(User $user);

    /**
     * save MembershipStatusNotification to the DB.
     * @param  MembershipStatusNotification $membershipStatusNotification
     */
    public function save(MembershipStatusNotification $membershipStatusNotification);
}
