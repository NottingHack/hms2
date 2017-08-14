<?php

namespace HMS\Entities\GateKeeper;

use Carbon\Carbon;
use HMS\Entities\User;

class AccessLog
{
    /**
     * Access was granted.
     */
    const ACCESS_GRANTED = 20;

    /**
     * Access was denied.
     */
    const ACCESS_DENIED = 10;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var Carbon
     */
    protected $accessTime;

    /**
     * @var null|string
     */
    protected $rfidSerial;

    /**
     * @var null|string
     */
    protected $pin;

    /**
     * @var int
     */
    protected $accessResult;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var Door
     */
    protected $door;

    /*
     * @var null|string
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
     * @return Carbon
     */
    public function getAccessTime()
    {
        return $this->accessTime;
    }

    /**
     * @param Carbon $accessTime
     *
     * @return self
     */
    public function setAccessTime(Carbon $accessTime)
    {
        $this->accessTime = $accessTime;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getRfidSerial()
    {
        return $this->rfidSerial;
    }

    /**
     * @param null|string $rfidSerial
     *
     * @return self
     */
    public function setRfidSerial(?string $rfidSerial)
    {
        $this->rfidSerial = $rfidSerial;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPin()
    {
        return $this->pin;
    }

    /**
     * @param null|string $pin
     *
     * @return self
     */
    public function setPin(?string $pin)
    {
        $this->pin = $pin;

        return $this;
    }

    /**
     * @return int
     */
    public function getAccessResult()
    {
        return $this->accessResult;
    }

    /**
     * @param int $accessResult
     *
     * @return self
     */
    public function setAccessResult($accessResult)
    {
        $this->accessResult = $accessResult;

        return $this;
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
     * @return Door
     */
    public function getDoor()
    {
        return $this->door;
    }

    /**
     * @param Door $door
     *
     * @return self
     */
    public function setDoor(Door $door)
    {
        $this->door = $door;

        return $this;
    }

    /**
     * @return Door
     */
    public function getDeniedReason()
    {
        return $this->deniedReason;
    }

    /**
     * @param Door $deniedReason
     *
     * @return self
     */
    public function setDeniedReason(Door $deniedReason)
    {
        $this->deniedReason = $deniedReason;

        return $this;
    }
}
