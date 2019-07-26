<?php

namespace HMS\Entities\Lighting;

class Pattern
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
     * @var Pattern|null
     */
    protected $nextPattern;

    /**
     * @var int|null
     */
    protected $timeout;

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
     * @return Pattern|null
     */
    public function getNextPattern()
    {
        return $this->nextPattern;
    }

    /**
     * @param Pattern|null $nextPattern
     *
     * @return self
     */
    public function setNextPattern($nextPattern)
    {
        $this->nextPattern = $nextPattern;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @param int|null $timeout
     *
     * @return self
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }
}
