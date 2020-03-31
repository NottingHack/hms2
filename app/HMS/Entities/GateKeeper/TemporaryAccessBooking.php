<?php

namespace HMS\Entities\GateKeeper;

use Carbon\Carbon;
use JsonSerializable;
use HMS\Entities\User;
use LaravelDoctrine\Extensions\Timestamps\Timestamps;
use Illuminate\Contracts\Support\Arrayable as ArrayableContract;

class TemporaryAccessBooking implements ArrayableContract, JsonSerializable
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
     * @var User
     */
    protected $user;

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
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'start' => $this->start->toAtomString(),
            'end' => $this->end->toAtomString(),
            'title' => $this->user->getFullName(),
            'userId' => $this->user->getId(),
        ];
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
