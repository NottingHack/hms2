<?php

namespace HMS\Entities\GateKeeper;

use Doctrine\Common\Collections\ArrayCollection;

class Zone
{
    const OFF_SITE = 'Off-site';

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
     * @var null|string
     */
    protected $permissionCode;

    /**
     * @var ArrayCollection|ZoneOccupant[]
     */
    protected $zoneOccupancts;

    /**
     * @var ArrayCollection|ZoneOccupancyLog[]
     */
    protected $zoneOccupancyLogs;

    /**
     * @var ArrayCollection|Room[]
     */
    protected $rooms;

    public function __construct()
    {
        $this->zoneOccupancts = new ArrayCollection();
        $this->zoneOccupancyLogs = new ArrayCollection();
        $this->rooms = new ArrayCollection();
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
     * @return null|string
     */
    public function getPermissionCode()
    {
        return $this->permissionCode;
    }

    /**
     * @return ArrayCollection|ZoneOccupant[]
     */
    public function getZoneOccupancts()
    {
        return $this->zoneOccupancts;
    }

    /**
     * @return ArrayCollection|ZoneOccupancyLog[]
     */
    public function getZoneOccupancyLogs()
    {
        return $this->zoneOccupancyLogs;
    }

    /**
     * @return ArrayCollection|Room[]
     */
    public function getRooms()
    {
        return $this->rooms;
    }
}
