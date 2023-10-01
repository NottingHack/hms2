<?php

namespace HMS\Entities\Banking;

use Carbon\Carbon;
use HMS\Entities\User;

class MembershipStatusNotification
{
    /**
     * The Notification was issued due to Non payment of membership.
     */
    public const NON_PAYMENT = 'NON_PAYMENT';

    /**
     * The Notification was issued due to payment but under the minimum threshold.
     */
    public const UNDER_MINIMUM_PAYMENT = 'UNDER_MINIMUM_PAYMENT';

    /**
     * The Notification was cleared due to a payment before membership was revoked.
     */
    public const PAYMENT = 'PAYMENT';

    /**
     * The Notification was cleared when the membership was revoked.
     */
    public const REVOKED = 'REVOKED';

    /**
     * The Notification was cleared manually, likely due to audit issues.
     */
    public const MANUAL = 'MANUAL';

    /**
     * @var int
     */
    protected $id;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var Account
     */
    protected $account;

    /**
     * @var BankTransaction
     */
    protected $bankTransaction;

    /**
     * @var Carbon
     */
    protected $timeIssued;

    /**
     * @var string
     */
    protected $issuedReason;

    /**
     * @var Carbon
     */
    protected $timeCleared;

    /**
     * @var string
     */
    protected $clearedReason;

    /**
     * MembershipStatusNotification constructor.
     */
    public function __construct()
    {
        $this->issuedReason = self::NON_PAYMENT;
    }

    /**
     * Gets the value of id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return self
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @param Account $account
     *
     * @return self
     */
    public function setAccount(Account $account)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * @return Carbon
     */
    public function getTimeIssued()
    {
        return $this->timeIssued;
    }

    /**
     * @param Carbon $timeIssued
     *
     * @return self
     */
    public function setTimeIssued(Carbon $timeIssued)
    {
        $this->timeIssued = $timeIssued;

        return $this;
    }

    /**
     * @return Carbon
     */
    public function getTimeCleared()
    {
        return $this->timeCleared;
    }

    /**
     * @param Carbon $timeCleared
     *
     * @return self
     */
    public function setTimeCleared(Carbon $timeCleared)
    {
        $this->timeCleared = $timeCleared;

        return $this;
    }

    /**
     * @return string
     */
    public function getClearedReason()
    {
        return $this->clearedReason;
    }

    /**
     * @param string $clearedReason
     *
     * @return self
     */
    public function setClearedReason($clearedReason)
    {
        $this->clearedReason = $clearedReason;

        return $this;
    }

    /**
     * Clear this notification and set the reason as paid.
     *
     * @return self
     */
    public function clearNotificationsByPayment()
    {
        $this->clearedReason = self::PAYMENT;
        $this->timeCleared = Carbon::now();

        return $this;
    }

    /**
     * Clear this notification and set the reason as revoked.
     *
     * @return self
     */
    public function clearNotificationsByRevoke()
    {
        $this->clearedReason = self::REVOKED;
        $this->timeCleared = Carbon::now();

        return $this;
    }

    /**
     * @return BankTransaction
     */
    public function getBankTransaction()
    {
        return $this->bankTransaction;
    }

    /**
     * @param BankTransaction $bankTransaction
     *
     * @return self
     */
    public function setBankTransaction(BankTransaction $bankTransaction)
    {
        $this->bankTransaction = $bankTransaction;

        return $this;
    }

    /**
     * @return string
     */
    public function getIssuedReason()
    {
        return $this->issuedReason;
    }

    /**
     * @param string $issuedReason
     *
     * @return self
     */
    public function setIssuedReason($issuedReason)
    {
        $this->issuedReason = $issuedReason;

        return $this;
    }

    /**
     * Set the issued reason to non payment.
     *
     * @return self
     */
    public function setIssuedReasonForNonPayment()
    {
        $this->issuedReason = self::NON_PAYMENT;

        return $this;
    }

    /**
     * Set the issued reason to under minimum payment.
     *
     * @return self
     */
    public function setIssuedReasonForUnderMinimumPayment()
    {
        $this->issuedReason = self::UNDER_MINIMUM_PAYMENT;

        return $this;
    }

    /**
     * Is this issued for non payment.
     *
     * @return bool
     */
    public function isForNonPayment()
    {
        return $this->issuedReason == self::NON_PAYMENT;
    }

    /**
     * Is this issued for under minimum payment.
     *
     * @return bool
     */
    public function isForUnderMinimumPayment()
    {
        return $this->issuedReason == self::UNDER_MINIMUM_PAYMENT;
    }
}
