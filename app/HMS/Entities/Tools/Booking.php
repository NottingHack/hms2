<?php

namespace HMS\Entities\Tools;

use HMS\Entities\User;
use HMS\Entities\Tools\Tool;
use HMS\Traits\Entities\SoftDeletable;
use LaravelDoctrine\Extensions\Timestamps\Timestamps;

class Booking
{
    use Timestamps, SoftDeletable;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var Carbon
     */
    protected $start;

    /**
     * @var Carbon
     */
    protected $end;

    /**
     * @var String
     */
    protected $type;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var Tool
     */
    protected $tool;

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
     * @return Carbon
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * @param Carbon $end
     *
     * @return self
     */
    public function setEnd(Carbon $end)
    {
        $this->end = $end;

        return $this;
    }

    /**
     * @return String
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param String $type
     *
     * @return self
     */
    public function setType(String $type)
    {
        $this->type = $type;

        return $this;
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
}
