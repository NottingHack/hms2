<?php

namespace HMS\Repositories\Banking;

use HMS\Entities\Banking\MembershipStatusNotification;

interface MembershipStatusNotificationRepository
{
    /**
     * find outtanding notifications.
     * @return MembershipStatusNotification[]
     */
    public function findOutstandingNotifications();

    /**
     * save MembershipStatusNotification to the DB.
     * @param  MembershipStatusNotification $membershipStatusNotification
     */
    public function save(MembershipStatusNotification $membershipStatusNotification);
}
