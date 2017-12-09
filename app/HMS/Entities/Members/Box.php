<?php

namespace HMS\Entities\Members;

use Carbon\Carbon;
use HMS\Entities\User;

class Box
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var Carbon
     */
    protected $boughtDate;

    /**
     * @var null|Carbon
     */
    protected $removedDate;

    /**
     * @var int
     */
    protected $state;

    /**
     * @var User
     */
    protected $user;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Carbon
     */
    public function getBoughtDate()
    {
        return $this->boughtDate;
    }

    /**
     * @param Carbon $boughtDate
     *
     * @return self
     */
    public function setBoughtDate(Carbon $boughtDate)
    {
        $this->boughtDate = $boughtDate;

        return $this;
    }

    /**
     * @return null|Carbon
     */
    public function getRemovedDate()
    {
        return $this->removedDate;
    }

    /**
     * @param null|Carbon $removedDate
     *
     * @return self
     */
    public function setRemovedDate(?Carbon $removedDate)
    {
        $this->removedDate = $removedDate;

        return $this;
    }

    /**
     * @return int
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Gets the value of state.
     *
     * @return string
     */
    public function getStateString()
    {
        return BoxState::STATE_STRINGS[$this->state];
    }

    /**
     * @param int $state
     *
     * @return self
     */
    public function setState($state)
    {
       if (array_key_exists($state, BoxState::STATE_STRINGS)) {
            $this->state = $state;
        }

        return $this;
    }

    /**
     * @return self
     */
    public function setStateInUse()
    {
        $this->state = BoxState::INUSE;
        $this->removedDate = null;

        return $this;
    }

    /**
     * @return self
     */
    public function setStateRemoved()
    {
        $this->state = BoxState::REMOVED;
        $this->removedDate = Carbon::now();

        return $this;
    }

    /**
     * @return self
     */
    public function setStateAbandoned()
    {
        $this->state = BoxState::ABANDONED;
        $this->removedDate = Carbon::now();

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


}