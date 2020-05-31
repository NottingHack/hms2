<?php

namespace HMS\Entities\Gatekeeper;

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

    /**
     * @param Zone $zone
     *
     * @return self
     */
    public function setZone(Zone $zone)
    {
        $this->zone = $zone;

        return $this;
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
     * @param Carbon $timeExited
     *
     * @return self
     */
    public function setTimeExited(Carbon $timeExited)
    {
        $this->timeExited = $timeExited;

        return $this;
    }

    /**
     * @param Carbon $timeEntered
     *
     * @return self
     */
    public function setTimeEntered(Carbon $timeEntered)
    {
        $this->timeEntered = $timeEntered;

        return $this;
    }
}
