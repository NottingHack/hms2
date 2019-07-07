<?php

namespace HMS\Entities\Tools;

use Carbon\Carbon;
use HMS\Entities\User;

class Usage
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var Tool
     */
    protected $tool;

    /**
     * @var Carbon
     */
    protected $start;

    /**
     * @var int
     */
    protected $duration;

    /**
     * @var int
     */
    protected $activeTime;

    /**
     * @var string
     */
    protected $status;

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
     * @return Tool
     */
    public function getTool()
    {
        return $this->tool;
    }

    /**
     * @return Carbon
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @return int
     */
    public function getActiveTime()
    {
        return $this->activeTime;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getStatusString()
    {
        return UsageState::STATE_STRINGS[$this->status];
    }
}
