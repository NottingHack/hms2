<?php

namespace HMS\Entities\Instrumentation;

use Doctrine\Common\Collections\ArrayCollection;
use HMS\Entities\Gatekeeper\Room;

class ElectricMeter
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
     * @var Room
     */
    protected $room;

    /**
     * @var \Doctrine\Common\Collections\Collection|ElectricReading[]
     */
    protected $readings;

    public function __construct()
    {
        $this->readings = new ArrayCollection();
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
     * @return Room
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     * @param Room $room
     *
     * @return self
     */
    public function setRoom(Room $room)
    {
        $this->room = $room;

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection|ElectricReading[]
     */
    public function getReadings()
    {
        return $this->readings;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection|ElectricReading[] $readings
     *
     * @return self
     */
    public function setReadings($readings)
    {
        $this->readings = $readings;

        return $this;
    }
}
