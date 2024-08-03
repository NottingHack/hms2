<?php

namespace HMS\Entities\Instrumentation;

class Humidity
{
    /**
     * @var string
     */
    protected $sensor;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var int
     */
    protected $reading;

    /**
     * @var Carbon
     */
    protected $time;

    /**
     * @return string
     */
    public function getSensor()
    {
        return $this->sensor;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return float
     */
    public function getReading()
    {
        return $this->reading;
    }

    /**
     * @return Carbon
     */
    public function getTime()
    {
        return $this->time;
    }
}
