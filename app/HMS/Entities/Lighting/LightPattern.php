<?php

namespace HMS\Entities\Lighting;

class LightPattern
{
    /**
     * @var Light
     */
    protected $light;

    /**
     * @var Pattern
     */
    protected $pattern;

    /**
     * @var string
     */
    protected $state;

    /**
     * @return Light
     */
    public function getLight()
    {
        return $this->light;
    }

    /**
     * @param Light $light
     *
     * @return self
     */
    public function setLight(Light $light)
    {
        $this->light = $light;

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
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     *
     * @return self
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }
}
