<?php

namespace HMS\Entities\GateKeeper;

use Carbon\Carbon;
use HMS\Entities\User;

class Pin
{
    /**
     * This pin can be used for entry (up until the expiry date), cannot be used to register a card.
     */
    const STATE_ACTIVE = 10;

    /**
     * Pin has expired and can no longer be used for entry.
     */
    const STATE_EXPIRED = 20;

    /**
     * This pin cannot be used for entry, and has likely been used to activate an RFID card.
     */
    const STATE_CANCELLED = 30;

    /**
     * This pin may be used to enrol an RFID card.
     */
    const STATE_ENROL = 40;
    /**
     * String representation of states for display.
     */
    public $statusStrings = [
                                  10 => 'Active',
                                  20 => 'Expired',
                                  30 => 'Cancelled',
                                  40 => 'Enrolment',
                                  ];

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
    public function __construct(string $pin, int $state = self::STATE_ACTIVE)
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
     * Sets the value of state.
     *
     * @param int $state the state
     *
     * @return self
     */
    public function setState($state)
    {
        if (in_array($state, array_keys($this->statusStrings))) {
            $this->state = $state;
        }

        return $this;
    }

    /**
     * Gets the value of state.
     *
     * @return string
     */
    public function getStateString()
    {
        return $this->statusStrings[$this->state];
    }

    /**
     * Sets the value of state to cancled.
     *
     * @return self
     */
    public function setStateCancelled()
    {
        $this->state = self::STATE_CANCELLED;

        return $this;
    }

    /**
     * Sets the value of state to enrol.
     *
     * @return self
     */
    public function setStateEnrol()
    {
        $this->state = self::STATE_ENROL;

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
