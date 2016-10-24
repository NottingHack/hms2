<?php

namespace HMS\Entities;

class Profile
{
    /** @var int The profiles unique id. */
    protected $id;

    /** @var User The user to which this profile belongs. */
    protected $user;

    /**
     * Profile constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
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
}
