<?php

namespace HMS\Entities\Instrumentation;

class SensorBattery
{
    /**
     * @var string
     */
    protected $sensor;

    /**
     * @var ?string
     */
    protected $name;

    /**
     * @var ?float
     */
    protected $reading;

    /**
     * @var \Carbon\Carbon
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
     * @return ?string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return ?float
     */
    public function getReading()
    {
        return $this->reading;
    }

    /**
     * @return \Carbon\Carbon
     */
    public function getTime()
    {
        return $this->time;
    }
}
