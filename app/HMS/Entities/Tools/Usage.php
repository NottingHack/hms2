<?php

namespace HMS\Entities\Tools;

use Carbon\Carbon;
use HMS\Entities\User;
use HMS\Entities\EntityObfuscatableInterface;

class Usage implements EntityObfuscatableInterface
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
     * @return Tool
     */
    public function getTool()
    {
        return $this->tool;
    }

    /**
     * @param Tool $tool
     *
     * @return self
     */
    public function setTool(Tool $tool)
    {
        $this->tool = $tool;

        return $this;
    }

    /**
     * @return Carbon
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @param Carbon $start
     *
     * @return self
     */
    public function setStart(Carbon $start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param int $duration
     *
     * @return self
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return int
     */
    public function getActiveTime()
    {
        return $this->activeTime;
    }

    /**
     * @param int $activeTime
     *
     * @return self
     */
    public function setActiveTime($activeTime)
    {
        $this->activeTime = $activeTime;

        return $this;
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

    /**
     * @param string $status
     *
     * @return self
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Disassociate the usage from a specific user
     */
    public function obfuscate() {
        $this->user = null;

        return $this;
    }
}
