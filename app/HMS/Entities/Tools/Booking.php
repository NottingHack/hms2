<?php

namespace HMS\Entities\Tools;

use Carbon\Carbon;
use JsonSerializable;
use HMS\Entities\User;
use LaravelDoctrine\Extensions\Timestamps\Timestamps;
use Illuminate\Contracts\Support\Arrayable as ArrayableContract;

class Booking implements ArrayableContract, JsonSerializable
{
    use Timestamps;

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
     * @var string
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
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getTypeString()
    {
        return BookingType::TYPE_STRINGS[$this->type];
    }

    /**
     * @param string $type
     *
     * @return self
     */
    public function setType(string $type)
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

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'start' => $this->start->toAtomString(),
            'end' => $this->end->toAtomString(),
            'title' => $this->user->getFullName(),
            'type' => $this->type,
            'toolId' => $this->tool->getId(),
            'toolDisplayName' => $this->tool->getDisplayName(),
            'userId' => $this->user->getId(),
        ];
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
