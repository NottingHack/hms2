<?php

namespace HMS\Entities\Gatekeeper;

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
     * @var \Doctrine\Common\Collections\Collection|ZoneOccupant[]
     */
    protected $zoneOccupancts;

    /**
     * @var \Doctrine\Common\Collections\Collection|ZoneOccupancyLog[]
     */
    protected $zoneOccupancyLogs;

    /**
     * @var \Doctrine\Common\Collections\Collection|Room[]
     */
    protected $rooms;

    /**
     * @var null|Building
     */
    protected $building;

    /**
     * @var bool
     */
    protected $restricted;

    public function __construct()
    {
        $this->zoneOccupancts = new ArrayCollection();
        $this->zoneOccupancyLogs = new ArrayCollection();
        $this->rooms = new ArrayCollection();
        $this->restricted = false;
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
     * @return \Doctrine\Common\Collections\Collection|ZoneOccupant[]
     */
    public function getZoneOccupancts()
    {
        return $this->zoneOccupancts;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection|ZoneOccupancyLog[]
     */
    public function getZoneOccupancyLogs()
    {
        return $this->zoneOccupancyLogs;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection|Room[]
     */
    public function getRooms()
    {
        return $this->rooms;
    }

    /**
     * @return null|Building
     */
    public function getBuilding()
    {
        return $this->building;
    }

    /**
     * @param null|Building $building
     *
     * @return self
     */
    public function setBuilding(Building $building)
    {
        $this->building = $building;

        return $this;
    }

    /**
     * @return bool
     */
    public function isRestricted()
    {
        return $this->restricted;
    }

    /**
     * @param bool $restricted
     *
     * @return self
     */
    public function setRestricted($restricted)
    {
        $this->restricted = $restricted;

        return $this;
    }
}
