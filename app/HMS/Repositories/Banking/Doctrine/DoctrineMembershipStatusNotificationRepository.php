<?php

namespace HMS\Repositories\Banking\Doctrine;

use HMS\Entities\User;
use Doctrine\ORM\EntityRepository;
use HMS\Entities\Banking\MembershipStatusNotification;
use HMS\Repositories\Banking\MembershipStatusNotificationRepository;

class DoctrineMembershipStatusNotificationRepository extends EntityRepository implements MembershipStatusNotificationRepository
{
    /**
     * find outtanding notifications.
     * @return MembershipStatusNotification[]
     */
    public function findOutstandingNotifications()
    {
        return parent::findByClearedReason(null);
    }

    /**
     * find outtanding notifications for a given user.
     * @return MembershipStatusNotification[]
     */
    public function findOutstandingNotificationsByUser(User $user)
    {
        return parent::findBy(['user' => $user, 'clearedReason' => null]);
    }

    /**
     * find by user.
     * @param  User $user
     * @return MembershipStatusNotification[]
     */
    public function findByUser(User $user)
    {
        return parent::findByUser($user);
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
