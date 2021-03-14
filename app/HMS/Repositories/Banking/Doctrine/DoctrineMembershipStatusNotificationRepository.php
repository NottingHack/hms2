<?php

namespace HMS\Repositories\Banking\Doctrine;

use Doctrine\ORM\EntityRepository;
use HMS\Entities\Banking\MembershipStatusNotification;
use HMS\Entities\User;
use HMS\Repositories\Banking\MembershipStatusNotificationRepository;

class DoctrineMembershipStatusNotificationRepository extends EntityRepository implements
    MembershipStatusNotificationRepository
{
    /**
     * Find outstanding notifications.
     *
     * @return MembershipStatusNotification[]
     */
    public function findOutstandingNotifications()
    {
        return parent::findByClearedReason(null);
    }

    /**
     * Find outstanding notifications for a given user.
     *
     * @param User $user
     *
     * @return MembershipStatusNotification[]
     */
    public function findOutstandingNotificationsByUser(User $user)
    {
        return parent::findBy(['user' => $user, 'clearedReason' => null]);
    }

    /**
     * Find by user.
     *
     * @param User $user
     *
     * @return MembershipStatusNotification[]
     */
    public function findByUser(User $user)
    {
        return parent::findByUser($user);
    }

    /**
     * Save MembershipStatusNotification to the DB.
     *
     * @param MembershipStatusNotification $membershipStatusNotification
     */
    public function save(MembershipStatusNotification $membershipStatusNotification)
    {
        $this->_em->persist($membershipStatusNotification);
        $this->_em->flush();
    }
}
