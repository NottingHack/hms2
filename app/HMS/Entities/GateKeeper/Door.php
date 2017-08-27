<?php

namespace HMS\Entities\GateKeeper;

use Carbon\Carbon;

class Door
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $shortName;

    /**
     * @var string
     */
    protected $state;

    /**
     * @var Carbon
     */
    protected $stateChange;

    /**
     * @var string
     */
    protected $permissionCode;

    /**
     * @var Zone
     */
    protected $sideAZone;

    /**
     * @var Zone
     */
    protected $sideBZone;

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
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getShortName()
    {
        return $this->shortName;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return Carbon
     */
    public function getStateChange()
    {
        return $this->stateChange;
    }

    /**
     * @return string
     */
    public function getPermissionCode()
    {
        return $this->permissionCode;
    }

    /**
     * @return Zone
     */
    public function getSideAZone()
    {
        return $this->sideAZone;
    }

    /**
     * @return Zone
     */
    public function getSideBZone()
    {
        return $this->sideBZone;
    }
}
