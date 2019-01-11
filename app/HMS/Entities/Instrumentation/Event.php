<?php

namespace HMS\Entities\Instrumentation;

use Carbon\Carbon;

class Event
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var Carbon
     */
    protected $time;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $value;

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
     * @return Carbon
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getTypeString()
    {
        return EventType::TYPE_STRINGS[$this->type];
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
