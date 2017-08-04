<?php

namespace HMS\Repositories\Banking\Doctrine;

use HMS\Entities\Banking\MembershipStatusNotification;
use Doctrine\ORM\EntityRepository;
use HMS\Repositories\Banking\MembershipStatusNotificationRepository;

class DoctrineMembershipStatusNotificationRepository extends EntityRepository implements MembershipStatusNotificationRepository
{
    /**
     * find outtanding notifications
     * @return MembershipStatusNotification[]
     */
    public function findOutstandingNotifications()
    {
        return parent::findByClearedReason(null);
    }

    /**
     * save MembershipStatusNotification to the DB.
     * @param  MembershipStatusNotification $membershipStatusNotification
     */
    public function save(MembershipStatusNotification $membershipStatusNotification)
    {
        $this->_em->persist($membershipStatusNotification);
        $this->_em->flush();
    }
}
