<?php

namespace HMS\Entities\GateKeeper;

use Carbon\Carbon;
use HMS\Entities\User;
use LaravelDoctrine\Extensions\Timestamps\Timestamps;

class TemporaryAccessBooking
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
     * @var string|null
     */
    protected $color;

    /**
     * @var string|null
     */
    protected $notes;

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
     * @return string|null
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param string|null $color
     *
     * @return self
     */
    public function setColor(?string $color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param string|null $notes
     *
     * @return self
     */
    public function setNotes(?string $notes)
    {
        $this->notes = $notes;

        return $this;
    }
}