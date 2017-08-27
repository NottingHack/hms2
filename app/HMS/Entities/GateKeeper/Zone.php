<?php

namespace HMS\Entities\GateKeeper;

class Zone
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
     * @var null|string
     */
    protected $permissionCode;

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
}
