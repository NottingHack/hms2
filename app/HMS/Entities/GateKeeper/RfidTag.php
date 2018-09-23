<?php

namespace HMS\Entities\GateKeeper;

use HMS\Entities\User;

class RfidTag
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
     * @var null|string
     */
    protected $rfidSerial;
    /**
     * @var null|string
     */
    protected $rfidSerialLegacy;
    /**
     * @var int
     */
    protected $state;
    /**
     * @var Carbon
     */
    protected $lastUsed;
    /**
     * @var string
     */
    protected $friendlyName;

    /**
     * @param null|string $rfidSerial
     * @param null|string $rfidSerialLegacy
     */
    public function __construct(?string $rfidSerial = null, ?string $rfidSerialLegacy = null)
    {
        $this->rfidSerial = $rfidSerial;
        $this->rfidSerialLegacy = $rfidSerialLegacy;
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

    /**
     * Gets the value of rfidSerial.
     *
     * @return null|string
     */
    public function getRfidSerial()
    {
        return $this->rfidSerial;
    }

    /**
     * Gets the value of rfidSerialLegacy.
     *
     * @return null|string
     */
    public function getRfidSerialLegacy()
    {
        return $this->rfidSerialLegacy;
    }

    /**
     * Gets the best serial.
     *
     * @return null|string
     */
    public function getBestRfidSerial()
    {
        return $this->rfidSerial ? $this->rfidSerial : $this->rfidSerialLegacy;
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
        if (array_key_exists($state, RfidTagState::STATE_STRINGS)) {
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
     * Gets the value of lastUsed.
     *
     * @return Carbon
     */
    public function getLastUsed()
    {
        return $this->lastUsed;
    }

    /**
     * Gets the value of friendlyName.
     *
     * @return string
     */
    public function getFriendlyName()
    {
        return $this->friendlyName;
    }

    /**
     * Sets the value of friendlyName.
     *
     * @param string $friendlyName the friendly name
     *
     * @return self
     */
    public function setFriendlyName(string $friendlyName)
    {
        $this->friendlyName = $friendlyName;

        return $this;
    }
}
