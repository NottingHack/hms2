<?php

namespace HMS\Entities\Lighting;

class OutputChannel
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $channel;

    /**
     * @var Controller
     */
    protected $controller;

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
     * @return int
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @param int $channel
     *
     * @return self
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;

        return $this;
    }

    /**
     * @return Controller
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @param Controller $controller
     *
     * @return self
     */
    public function setController(Controller $controller)
    {
        $this->controller = $controller;

        return $this;
    }
}
