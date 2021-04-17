<?php

namespace HMS\Entities\Instrumentation;

use Carbon\Carbon;

class Temperature
{
    /**
     * @var string
     */
    protected $dallasAddress;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var float
     */
    protected $temperature;

    /**
     * @var Carbon
     */
    protected $time;

    /**
     * @return string
     */
    public function getDallasAddress()
    {
        return $this->dallasAddress;
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
    public function getTemperature()
    {
        return $this->temperature;
    }

    /**
     * @return Carbon
     */
    public function getTime()
    {
        return $this->time;
    }
}
