<?php

namespace HMS\Entities\Instrumentation;

use Carbon\Carbon;

class ElectricReading
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $reading;

    /**
     * @var Carbon
     */
    protected $date;

    /**
     * @var ElectricMeter
     */
    protected $meter;

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
    public function getReading()
    {
        return $this->reading;
    }

    /**
     * @param int $reading
     *
     * @return self
     */
    public function setReading($reading)
    {
        $this->reading = $reading;

        return $this;
    }

    /**
     * @return Carbon
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param Carbon $date
     *
     * @return self
     */
    public function setDate(Carbon $date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return ElectricMeter
     */
    public function getMeter()
    {
        return $this->meter;
    }

    /**
     * @param ElectricMeter $meter
     *
     * @return self
     */
    public function setMeter(ElectricMeter $meter)
    {
        $this->meter = $meter;

        return $this;
    }
}
