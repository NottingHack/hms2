<?php

namespace HMS\Entities\Gatekeeper;

use Doctrine\Common\Collections\ArrayCollection;

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
     * @var \Doctrine\Common\Collections\Collection|Door[]
     */
    protected $doors;

    /**
     * Bell constructor.
     */
    public function __construct()
    {
        $this->doors = new ArrayCollection();
        $this->enabled = true;
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
     * @return \Doctrine\Common\Collections\Collection|Door[]
     */
    public function getDoors()
    {
        return $this->doors;
    }
}
