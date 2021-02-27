<?php

namespace HMS\Entities\Gatekeeper;

use Doctrine\Common\Collections\ArrayCollection;

class Floor
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var int
     */
    protected $level;

    /**
     * @var Building
     */
    protected $building;

    /**
     * @var \Doctrine\Common\Collections\Collection|Room[]
     */
    protected $rooms;

    public function __construct()
    {
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param int $level
     *
     * @return self
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * @return Building
     */
    public function getBuilding()
    {
        return $this->building;
    }

    /**
     * @param Building $building
     *
     * @return self
     */
    public function setBuilding(Building $building)
    {
        $this->building = $building;

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection|Room[]
     */
    public function getRooms()
    {
        return $this->rooms;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection|Room[] $rooms
     *
     * @return self
     */
    public function setRooms($rooms)
    {
        $this->rooms = $rooms;

        return $this;
    }
}
