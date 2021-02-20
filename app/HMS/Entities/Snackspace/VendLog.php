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
     * @var \HMS\Entities\Snackspace\Transaction
     */
    protected $transaction;

    /**
     * @var \HMS\Entities\Snackspace\VendingMachine
     */
    protected $vendingMachine;

    /**
     * @var string|null
     */
    protected $rfidSerial;

    /**
     * @var User|null
     */
    protected $user;

    /**
     * @var Carbon|null
     */
    protected $enqueuedTime;

    /**
     * @var Carbon|null
     */
    protected $requestTime;

    /**
     * @var Carbon|null
     */
    protected $successTime;

    /**
     * @var Carbon|null
     */
    protected $cancelledTime;

    /**
     * @var Carbon|null
     */
    protected $failedTime;

    /**
     * @var int|null
     */
    protected $amountScaled;

    /**
     * @var string|null
     */
    protected $position;

    /**
     * @var string|null
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
     * @return \HMS\Entities\Snackspace\Transaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * @return \HMS\Entities\Snackspace\VendingMachine
     */
    public function getVendingMachine()
    {
        return $this->vendingMachine;
    }

    /**
     * @return string|null
     */
    public function getRfidSerial()
    {
        return $this->rfidSerial;
    }

    /**
     * @return User|null
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return Carbon|null
     */
    public function getEnqueuedTime()
    {
        return $this->enqueuedTime;
    }

    /**
     * @return Carbon|null
     */
    public function getRequestTime()
    {
        return $this->requestTime;
    }

    /**
     * @return Carbon|null
     */
    public function getSuccessTime()
    {
        return $this->successTime;
    }

    /**
     * @return Carbon|null
     */
    public function getCancelledTime()
    {
        return $this->cancelledTime;
    }

    /**
     * @return Carbon|null
     */
    public function getFailedTime()
    {
        return $this->failedTime;
    }

    /**
     * @return int|null
     */
    public function getAmountScaled()
    {
        return $this->amountScaled;
    }

    /**
     * @return string|null
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @return string|null
     */
    public function getDeniedReason()
    {
        return $this->deniedReason;
    }

    /**
     * @param Carbon|null $successTime
     *
     * @return self
     */
    public function setSuccessTime($successTime)
    {
        $this->successTime = $successTime;

        return $this;
    }

    /**
     * @param Carbon|null $failedTime
     *
     * @return self
     */
    public function setFailedTime($failedTime)
    {
        $this->failedTime = $failedTime;

        return $this;
    }

    /**
     * @param string|null $position
     *
     * @return self
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }
}
