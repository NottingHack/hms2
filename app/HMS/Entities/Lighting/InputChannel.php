<?php

namespace HMS\Entities\Lighting;

class InputChannel
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
     * @var Pattern
     */
    protected $pattern;

    /**
     * @var bool
     */
    protected $statefull;

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

    /**
     * @return Pattern
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * @param Pattern $pattern
     *
     * @return self
     */
    public function setPattern(Pattern $pattern)
    {
        $this->pattern = $pattern;

        return $this;
    }

    /**
     * @return bool
     */
    public function isStatefull()
    {
        return $this->statefull;
    }

    /**
     * @param bool $statefull
     *
     * @return self
     */
    public function setStatefull($statefull)
    {
        $this->statefull = $statefull;

        return $this;
    }
}
