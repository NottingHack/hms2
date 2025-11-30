<?php

namespace HMS\Factories\Banking;

use HMS\Entities\Banking\Account;
use HMS\Entities\Banking\MembershipStatusNotification;
use HMS\Entities\User;
use HMS\Repositories\Banking\BankTransactionRepository;
use HMS\Repositories\Banking\MembershipStatusNotificationRepository;

class MembershipStatusNotificationFactory
{
    /**
     * @var MembershipStatusNotificationRepository
     */
    protected $membershipStatusNotificationRepository;

    /**
     * @var BankTransactionRepository
     */
    protected $bankTransactionRepository;

    /**
     * @param MembershipStatusNotificationRepository $membershipStatusNotificationRepository
     * @param BankTransactionRepository $bankTransactionRepository
     */
    public function __construct(
        MembershipStatusNotificationRepository $membershipStatusNotificationRepository,
        BankTransactionRepository $bankTransactionRepository
    ) {
        $this->membershipStatusNotificationRepository = $membershipStatusNotificationRepository;
        $this->bankTransactionRepository = $bankTransactionRepository;
    }

    /**
     * Function to instantiate a new non payment MembershipStatusNotification from given params.
     *
     * @param User $user
     * @param Account $account
     *
     * @return MembershipStatusNotification
     */
    public function createForNonPayment(User $user, Account $account)
    {
        $_membershipStatusNotification = new MembershipStatusNotification();
        $_membershipStatusNotification->setUser($user);
        $_membershipStatusNotification->setAccount($account);
        $_membershipStatusNotification->setIssuedReasonForNonPayment();

        return $_membershipStatusNotification;
    }

    /**
     * Function to instantiate a new under minimum payment MembershipStatusNotification from given params.
     *
     * @param User $user
     * @param Account $account
     *
     * @return MembershipStatusNotification
     */
    public function createForUnderPayment(User $user, Account $account)
    {
        $_membershipStatusNotification = new MembershipStatusNotification();
        $_membershipStatusNotification->setUser($user);
        $_membershipStatusNotification->setAccount($account);
        $_membershipStatusNotification->setIssuedReasonForUnderMinimumPayment();
        $_membershipStatusNotification->setBankTransaction(
            $this->bankTransactionRepository->findLatestTransactionByAccount($account)
        );

        return $_membershipStatusNotification;
    }
}
