<?php

namespace HMS\Entities;

use Carbon\Carbon;
use HMS\Traits\Entities\Timestampable;

class Profile
{
    use Timestampable;

    /**
     * @var User The user to which this profile belongs.
     */
    protected $user;

    /**
     * @var null|Carbon
     */
    protected $joinDate;

    /**
     * @var null|string
     */
    protected $unlockText;

    /**
     * @var int
     */
    protected $creditLimit;

    /**
     * @var null|string
     */
    protected $address1;

    /**
     * @var null|string
     */
    protected $address2;

    /**
     * @var null|string
     */
    protected $address3;

    /**
     * @var null|string
     */
    protected $addressCity;

    /**
     * @var null|string
     */
    protected $addressCounty;

    /**
     * @var null|string
     */
    protected $addressPostcode;

    /**
     * @var null|string
     */
    protected $contactNumber;

    /**
     * @var null|Carbon
     */
    protected $dateOfBirth;

    /**
     * @var int
     */
    protected $balance;

    /**
     * Has the contactNumber been verified.
     *
     * @var bool
     */
    protected $contactNumberVerified;

    /**
     * Profile constructor.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;

        // setup defaults.
        $this->creditLimit = 0;
        $this->balance = 0;
        $this->contactNumberVerified = false;
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
     *
     * @return self
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return null|Carbon
     */
    public function getJoinDate(): ?Carbon
    {
        return $this->joinDate;
    }

    /**
     * @param null|Carbon $joinDate
     *
     * @return self
     */
    public function setJoinDate(?Carbon $joinDate): self
    {
        $this->joinDate = $joinDate;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getUnlockText(): ?string
    {
        return $this->unlockText;
    }

    /**
     * @param null|string $unlockText
     *
     * @return self
     */
    public function setUnlockText(?string $unlockText): self
    {
        $this->unlockText = $unlockText;

        return $this;
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
     *
     * @return self
     */
    public function setCreditLimit(int $creditLimit): self
    {
        $this->creditLimit = $creditLimit;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getAddress1(): ?string
    {
        return $this->address1;
    }

    /**
     * @param null|string $address1
     *
     * @return self
     */
    public function setAddress1(?string $address1): self
    {
        $this->address1 = $address1;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddress2(): ?string
    {
        return $this->address2;
    }

    /**
     * @param string $address2
     *
     * @return self
     */
    public function setAddress2(?string $address2): self
    {
        $this->address2 = $address2;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddress3(): ?string
    {
        return $this->address3;
    }

    /**
     * @param string $address3
     *
     * @return self
     */
    public function setAddress3(?string $address3): self
    {
        $this->address3 = $address3;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddressCity(): ?string
    {
        return $this->addressCity;
    }

    /**
     * @param string $addressCity
     *
     * @return self
     */
    public function setAddressCity(?string $addressCity): self
    {
        $this->addressCity = $addressCity;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddressCounty(): ?string
    {
        return $this->addressCounty;
    }

    /**
     * @param string $addressCounty
     *
     * @return self
     */
    public function setAddressCounty(?string $addressCounty): self
    {
        $this->addressCounty = $addressCounty;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddressPostcode(): ?string
    {
        return $this->addressPostcode;
    }

    /**
     * @param string $addressPostcode
     *
     * @return self
     */
    public function setAddressPostcode(?string $addressPostcode): self
    {
        $this->addressPostcode = $addressPostcode;

        return $this;
    }

    /**
     * @return string
     */
    public function getContactNumber(): ?string
    {
        return $this->contactNumber;
    }

    /**
     * @param null|string $contactNumber
     *
     * @return self
     */
    public function setContactNumber(?string $contactNumber): self
    {
        $this->contactNumber = $contactNumber;
        $this->contactNumberVerified = false;

        return $this;
    }

    /**
     * @return null|Carbon
     */
    public function getDateOfBirth(): ?Carbon
    {
        if ($this->dateOfBirth) {
            return Carbon::instance($this->dateOfBirth);
        }

        return null;
    }

    /**
     * @param null|Carbon $dateOfBirth
     *
     * @return self
     */
    public function setDateOfBirth(?Carbon $dateOfBirth): self
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    /**
     * @return int
     */
    public function getBalance(): int
    {
        return $this->balance;
    }

    /**
     * @param int $balance
     *
     * @return self
     */
    public function setBalance(int $balance): self
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * @param int $amount
     *
     * @return self
     */
    public function updateBalanceByAmount(int $amount): self
    {
        $this->balance += $amount;

        return $this;
    }

    /**
     * @return bool
     */
    public function isContactNumberVerified(): bool
    {
        return $this->contactNumberVerified;
    }

    /**
     * @param bool $contactNumberVerified
     *
     * @return self
     */
    public function setContactNumberVerified(bool $contactNumberVerified): self
    {
        $this->contactNumberVerified = $contactNumberVerified;

        return $this;
    }
}
