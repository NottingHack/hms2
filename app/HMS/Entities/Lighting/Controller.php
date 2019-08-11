<?php

namespace HMS\Entities\Lighting;

use HMS\Entities\GateKeeper\Room;

class Controller
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
}
