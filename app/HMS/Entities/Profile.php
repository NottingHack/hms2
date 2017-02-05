<?php

namespace HMS\Entities;

use Carbon\Carbon;
use HMS\Traits\Entities\Timestampable;

class Profile
{
    use Timestampable;

    /** @var User The user to which this profile belongs. */
    protected $user;

    /** @var Carbon */
    protected $joinDate;

    /** @var string */
    protected $unlockText;

    /** @var int */
    protected $creditLimit;

    /** @var string */
    protected $address1;

    /** @var string */
    protected $address2;

    /** @var string */
    protected $address3;

    /** @var string */
    protected $addressCity;

    /** @var string */
    protected $addressCounty;

    /** @var string */
    protected $addressPostcode;

    /** @var string */
    protected $contactNumber;

    /**
     * Profile constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;

        // setup defaults.
        $this->creditLimit = 0;
    }

    /**
     * @return User
     */
    public function getUser() : User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return Carbon
     */
    public function getJoinDate(): Carbon
    {
        return $this->joinDate;
    }

    /**
     * @param Carbon $joinDate
     */
    public function setJoinDate(Carbon $joinDate)
    {
        $this->joinDate = $joinDate;
    }

    /**
     * @return string
     */
    public function getUnlockText(): string
    {
        return $this->unlockText;
    }

    /**
     * @param string $unlockText
     */
    public function setUnlockText(string $unlockText)
    {
        $this->unlockText = $unlockText;
    }

    /**
     * @return int
     */
    public function getCreditLimit(): int
    {
        return $this->creditLimit;
    }

    /**
     * @param int $creditLimit
     */
    public function setCreditLimit(int $creditLimit)
    {
        $this->creditLimit = $creditLimit;
    }

    /**
     * @return string
     */
    public function getAddress1(): string
    {
        return $this->address1;
    }

    /**
     * @param string $address1
     */
    public function setAddress1(string $address1)
    {
        $this->address1 = $address1;
    }

    /**
     * @return string
     */
    public function getAddress2(): string
    {
        return $this->address2;
    }

    /**
     * @param string $address2
     */
    public function setAddress2(string $address2)
    {
        $this->address2 = $address2;
    }

    /**
     * @return string
     */
    public function getAddress3(): string
    {
        return $this->address3;
    }

    /**
     * @param string $address3
     */
    public function setAddress3(string $address3)
    {
        $this->address3 = $address3;
    }

    /**
     * @return string
     */
    public function getAddressCity(): string
    {
        return $this->addressCity;
    }

    /**
     * @param string $addressCity
     */
    public function setAddressCity(string $addressCity)
    {
        $this->addressCity = $addressCity;
    }

    /**
     * @return string
     */
    public function getAddressCounty(): string
    {
        return $this->addressCounty;
    }

    /**
     * @param string $addressCounty
     */
    public function setAddressCounty(string $addressCounty)
    {
        $this->addressCounty = $addressCounty;
    }

    /**
     * @return string
     */
    public function getAddressPostcode(): string
    {
        return $this->addressPostcode;
    }

    /**
     * @param string $addressPostcode
     */
    public function setAddressPostcode(string $addressPostcode)
    {
        $this->addressPostcode = $addressPostcode;
    }

    /**
     * @return string
     */
    public function getContactNumber(): string
    {
        return $this->contactNumber;
    }

    /**
     * @param string $contactNumber
     */
    public function setContactNumber(string $contactNumber)
    {
        $this->contactNumber = $contactNumber;
    }
}
