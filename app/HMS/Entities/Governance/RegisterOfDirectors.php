<?php

namespace HMS\Entities\Governance;

use Carbon\Carbon;
use HMS\Entities\User;
use HMS\Traits\Entities\Timestampable;

class RegisterOfDirectors
{
    use Timestampable;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var string
     */
    protected $firstname;

    /**
     * @var string
     */
    protected $lastname;

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
     * @var Carbon
     */
    protected $startedAt;

    /**
     * @var null|Carbon
     */
    protected $endedAt;

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
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     *
     * @return self
     */
    public function setFirstname(string $firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     *
     * @return self
     */
    public function setLastname(string $lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * @return string
     */
    public function getFullname(): string
    {
        return $this->firstname . ' ' . $this->lastname;
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
     * @return Carbon
     */
    public function getStartedAt()
    {
        return $this->startedAt;
    }

    /**
     * @param Carbon $startedAt
     *
     * @return self
     */
    public function setStartedAt(Carbon $startedAt)
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    /**
     * @return null|Carbon
     */
    public function getEndedAt()
    {
        return $this->endedAt;
    }

    /**
     * @param null|Carbon $endedAt
     *
     * @return self
     */
    public function setEndedAt(?Carbon $endedAt)
    {
        $this->endedAt = $endedAt;

        return $this;
    }
}
