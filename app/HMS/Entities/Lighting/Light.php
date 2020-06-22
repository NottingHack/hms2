<?php

namespace HMS\Entities\Lighting;

use HMS\Entities\Gatekeeper\Room;

class Light
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string|null
     */
    protected $name;

    /**
     * @var Room
     */
    protected $room;

    /**
     * @var OutputChannel
     */
    protected $outputChannel;

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
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string|null $name
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
     * @return OutputChannel
     */
    public function getOutputChannel()
    {
        return $this->outputChannel;
    }

    /**
     * @param OutputChannel $outputChannel
     *
     * @return self
     */
    public function setOutputChannel(OutputChannel $outputChannel)
    {
        $this->outputChannel = $outputChannel;

        return $this;
    }
}
