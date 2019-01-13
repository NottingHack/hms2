<?php

namespace HMS\Entities\Snackspace;

use Carbon\Carbon;
use HMS\Entities\User;

class VendLog
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var HMS\Entities\Snackspace\Transaction
     */
    protected $transaction;

    /**
     * @var HMS\Entities\Snackspace\VendingMachine
     */
    protected $vendingMachine;

    /**
     * @var string
     */
    protected $rfidSerial;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var Carbon
     */
    protected $enqueuedTime;

    /**
     * @var Carbon
     */
    protected $requestTime;

    /**
     * @var Carbon
     */
    protected $successTime;

    /**
     * @var Carbon
     */
    protected $cancelledTime;

    /**
     * @var Carbon
     */
    protected $failedTime;

    /**
     * @var int
     */
    protected $amountScaled;

    /**
     * @var string
     */
    protected $position;

    /**
     * @var string
     */
    protected $deniedReason;

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
     * @return HMS\Entities\Snackspace\Transaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * @return HMS\Entities\Snackspace\VendingMachine
     */
    public function getVendingMachine()
    {
        return $this->vendingMachine;
    }

    /**
     * @return string
     */
    public function getRfidSerial()
    {
        return $this->rfidSerial;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return Carbon
     */
    public function getEnqueuedTime()
    {
        return $this->enqueuedTime;
    }

    /**
     * @return Carbon
     */
    public function getRequestTime()
    {
        return $this->requestTime;
    }

    /**
     * @return Carbon
     */
    public function getSuccessTime()
    {
        return $this->successTime;
    }

    /**
     * @return Carbon
     */
    public function getCancelledTime()
    {
        return $this->cancelledTime;
    }

    /**
     * @return Carbon
     */
    public function getFailedTime()
    {
        return $this->failedTime;
    }

    /**
     * @return int
     */
    public function getAmountScaled()
    {
        return $this->amountScaled;
    }

    /**
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @return string
     */
    public function getDeniedReason()
    {
        return $this->deniedReason;
    }
}
