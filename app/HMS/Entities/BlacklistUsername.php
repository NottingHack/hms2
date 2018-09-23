<?php

namespace HMS\Entities;

class BlacklistUsername
{
    /**
     * @var string
     */
    protected $username;

    /**
     * Gets the value of username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }
}
