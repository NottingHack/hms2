<?php

namespace HMS\Factories\Banking;

use HMS\Entities\User;
use HMS\Entities\Banking\Account;
use HMS\Entities\Banking\MembershipStatusNotification;
use HMS\Repositories\Banking\MembershipStatusNotificationRepository;

class MembershipStatusNotificationFactory
{
    /**
     * @var MembershipStatusNotificationRepository
     */
    protected $membershipStatusNotificationRepository;

    /**
     * @param MembershipStatusNotificationRepository $membershipStatusNotificationRepository
     */
    public function __construct(MembershipStatusNotificationRepository $membershipStatusNotificationRepository)
    {
        $this->membershipStatusNotificationRepository = $membershipStatusNotificationRepository;
    }

    /**
     * Function to instantiate a new MembershipStatusNotification from given params.
     */
    public function create(User $user, Account $account)
    {
        $_membershipStatusNotification = new MembershipStatusNotification();
        $_membershipStatusNotification->setUser($user);
        $_membershipStatusNotification->setAccount($account);

        return $_membershipStatusNotification;
    }
}
