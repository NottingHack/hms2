<?php

namespace HMS\Entities\Instrumentation;

use Carbon\Carbon;

class MacAddress
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $macAddress;

    /**
     * @var Carbon
     */
    protected $lastSeen;

    /**
     * @var bool
     */
    protected $ignore;

    /**
     * @var string
     */
    protected $comments;

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
    public function getMacAddress()
    {
        return $this->macAddress;
    }

    /**
     * @param string $macAddress
     *
     * @return self
     */
    public function setMacAddress($macAddress)
    {
        $this->macAddress = $macAddress;

        return $this;
    }

    /**
     * @return Carbon
     */
    public function getLastSeen()
    {
        return $this->lastSeen;
    }

    /**
     * @return bool
     */
    public function isIgnore()
    {
        return $this->ignore;
    }

    /**
     * @param bool $ignore
     *
     * @return self
     */
    public function setIgnore($ignore)
    {
        $this->ignore = $ignore;

        return $this;
    }

    /**
     * @return string
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param string $comments
     *
     * @return self
     */
    public function setComments($comments)
    {
        $this->comments = $comments;

        return $this;
    }
}
