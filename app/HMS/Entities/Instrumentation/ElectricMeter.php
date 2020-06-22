<?php

namespace HMS\Entities\Instrumentation;

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
     * @var ElectricReading[]|ArrayCollection()
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
     * @return ElectricReading[]|ArrayCollection()
     */
    public function getReadings()
    {
        return $this->readings;
    }

    /**
     * @param ElectricReading[]|ArrayCollection() $readings
     *
     * @return self
     */
    public function setReadings($readings)
    {
        $this->readings = $readings;

        return $this;
    }
}
