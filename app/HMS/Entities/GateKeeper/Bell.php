<?php

namespace HMS\Entities\GateKeeper;

class Bell
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $topic;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var bool
     */
    protected $enabled;

    /**
     * @var HMS\Entities\GateKeeper\Door[]|ArrayCollection
     */
    protected $doors;

    /**
     * Bell constructor.
     */
    public function __construct()
    {
        $this->doors = new ArrayCollection();
    }

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
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getTopic()
    {
        return $this->topic;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @return HMS\Entities\GateKeeper\Door[]|ArrayCollection
     */
    public function getDoors()
    {
        return $this->doors;
    }
}
