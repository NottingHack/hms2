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
