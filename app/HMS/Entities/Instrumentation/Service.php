<?php

namespace HMS\Entities\Instrumentation;

use Carbon\Carbon;

class Service
{
    /**
     * Primary key.
     *
     * @var string
     */
    protected $name;

    /**
     * @var int
     */
    protected $status;

    /**
     * @var string
     */
    protected $statusString;

    /**
     * @var Carbon
     */
    protected $queryTime;

    /**
     * @var Carbon
     */
    protected $replyTime;

    /**
     * @var Carbon
     */
    protected $restartTime;

    /**
     * @var string
     */
    protected $description;

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
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getStatusString()
    {
        return $this->statusString;
    }

    /**
     * @return Carbon
     */
    public function getQueryTime()
    {
        return $this->queryTime;
    }

    /**
     * @return Carbon
     */
    public function getReplyTime()
    {
        return $this->replyTime;
    }

    /**
     * @return Carbon
     */
    public function getRestartTime()
    {
        return $this->restartTime;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
