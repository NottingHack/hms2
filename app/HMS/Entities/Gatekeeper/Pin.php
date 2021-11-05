<?php

namespace HMS\Entities\Gatekeeper;

use Carbon\Carbon;
use HMS\Entities\User;

class Pin
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $pin;

    /**
     * @var Carbon
     */
    protected $dateAdded;

    /**
     * @var Carbon
     */
    protected $expiry;

    /**
     * @var int
     */
    protected $state;

    /**
     * @var User
     */
    protected $user;

    /**
     * @param string $pin
     * @param int $state
     */
    public function __construct(string $pin, int $state = PinState::ACTIVE)
    {
        $this->pin = $pin;
        $this->state = $state;
    }

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
     * Gets the value of pin.
     *
     * @return string
     */
    public function getPin()
    {
        return $this->pin;
    }

    /**
     * Sets the value of pin.
     *
     * @param string $pin the pin
     *
     * @return self
     */
    public function setPin($pin)
    {
        $this->pin = $pin;

        return $this;
    }

    /**
     * Gets the value of dateAdded.
     *
     * @return Carbon
     */
    public function getDateAdded()
    {
        return $this->dateAdded;
    }

    /**
     * Sets the value of dateAdded.
     *
     * @param Carbon $dateAdded the date added
     *
     * @return self
     */
    public function setDateAdded(Carbon $dateAdded)
    {
        $this->dateAdded = $dateAdded;

        return $this;
    }

    /**
     * Gets the value of expiry.
     *
     * @return Carbon
     */
    public function getExpiry()
    {
        return $this->expiry;
    }

    /**
     * Sets the value of expiry.
     *
     * @param Carbon $expiry the expiry
     *
     * @return self
     */
    public function setExpiry(Carbon $expiry)
    {
        $this->expiry = $expiry;

        return $this;
    }

    /**
     * Gets the value of state.
     *
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
        return PinState::STATE_STRINGS[$this->state];
    }

    /**
     * Sets the value of state.
     *
     * @param int $state the state
     *
     * @return self
     */
    public function setState($state)
    {
        if (array_key_exists($state, PinState::STATE_STRINGS)) {
            $this->state = $state;
        }

        return $this;
    }

    /**
     * Sets the value of state to cancled.
     *
     * @return self
     */
    public function setStateCancelled()
    {
        $this->state = PinState::CANCELLED;

        return $this;
    }

    /**
     * Sets the value of state to enroll.
     *
     * @return self
     */
    public function setStateEnroll()
    {
        $this->state = PinState::ENROLL;

        return $this;
    }

    /**
     * Gets the value of user.
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Sets the value of user.
     *
     * @param User $user the user
     *
     * @return self
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }
}
