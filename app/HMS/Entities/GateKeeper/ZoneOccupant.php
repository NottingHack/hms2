<?php

namespace HMS\Entities\GateKeeper;

use Carbon\Carbon;
use HMS\Entities\User;

class ZoneOccupant
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var Zone
     */
    protected $zone;

    /**
     * @var Carbon
     */
    protected $timeEntered;

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
     * @return Zone
     */
    public function getZone()
    {
        return $this->zone;
    }

    /**
     * @return Carbon
     */
    public function getTimeEntered()
    {
        return $this->timeEntered;
    }
}
