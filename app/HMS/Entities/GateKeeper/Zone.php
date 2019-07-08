<?php

namespace HMS\Entities\GateKeeper;

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
     * @var ZoneOccupant[]
     */
    protected $zoneOccupancts;

    /**
     * @var ZoneOccupancyLog[]
     */
    protected $zoneOccupancyLogs;

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
     * @return ZoneOccupant[]
     */
    public function getZoneOccupancts()
    {
        return $this->zoneOccupancts;
    }

    /**
     * @return ZoneOccupancyLog[]
     */
    public function getZoneOccupancyLogs()
    {
        return $this->zoneOccupancyLogs;
    }
}
