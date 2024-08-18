<?php

namespace HMS\Entities\Governance;

use Carbon\Carbon;
use HMS\Entities\User;
use HMS\Traits\Entities\Timestampable;

class RegisterOfMembers
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
