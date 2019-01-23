<?php

namespace HMS\Entities\GateKeeper;

use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;

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
     * @var Zone
     */
    protected $sideAZone;

    /**
     * @var Zone
     */
    protected $sideBZone;

    /**
     * @var Bell
     */
    protected $bells;

    /**
     * Door constructor.
     */
    public function __construct()
    {
        $this->bells = new ArrayCollection();
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

    /**
     * @return Bell
     */
    public function getBells()
    {
        return $this->bells;
    }
}
