<?php

namespace HMS\Entities\GateKeeper;

use Carbon\Carbon;
use HMS\Entities\User;

class ZoneOccupancyLog
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var Zone
     */
    protected $zone;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var Carbon
     */
    protected $timeExited;

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
     * @return Zone
     */
    public function getZone()
    {
        return $this->zone;
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
    public function getTimeExited()
    {
        return $this->timeExited;
    }

    /**
     * @return Carbon
     */
    public function getTimeEntered()
    {
        return $this->timeEntered;
    }
}
