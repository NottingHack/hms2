<?php

namespace HMS\Entities\Governance;

use Carbon\Carbon;
use HMS\Entities\User;
use Doctrine\Common\Collections\ArrayCollection;

class Meeting
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var Carbon
     */
    protected $startTime;

    /**
     * @var bool
     */
    protected $extraordinary;

    /**
     * @var int
     */
    protected $currentMembers;

    /**
     * @var int
     */
    protected $votingMembers;

    /**
     * @var int
     */
    protected $quorum;

    /**
     * @var \Doctrine\Common\Collections\Collection|User[]
     */
    protected $attendees;

    /**
     * @var \Doctrine\Common\Collections\Collection|Proxy[]
     */
    protected $proxies;

    /**
     * Meeting constructor.
     */
    public function __construct()
    {
        $this->attendees = new ArrayCollection();
        $this->proxies = new ArrayCollection();
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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Carbon
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @param Carbon $startTime
     *
     * @return self
     */
    public function setStartTime(Carbon $startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * @return bool
     */
    public function isExtraordinary()
    {
        return $this->extraordinary;
    }

    /**
     * @param bool $extraordinary
     *
     * @return self
     */
    public function setExtraordinary($extraordinary)
    {
        $this->extraordinary = $extraordinary;

        return $this;
    }

    /**
     * @return int
     */
    public function getCurrentMembers()
    {
        return $this->currentMembers;
    }

    /**
     * @param int $currentMembers
     *
     * @return self
     */
    public function setCurrentMembers($currentMembers)
    {
        $this->currentMembers = $currentMembers;

        return $this;
    }

    /**
     * @return int
     */
    public function getVotingMembers()
    {
        return $this->votingMembers;
    }

    /**
     * @param int $votingMembers
     *
     * @return self
     */
    public function setVotingMembers($votingMembers)
    {
        $this->votingMembers = $votingMembers;

        return $this;
    }

    /**
     * @return int
     */
    public function getQuorum()
    {
        return $this->quorum;
    }

    /**
     * @param int $quorum
     *
     * @return self
     */
    public function setQuorum($quorum)
    {
        $this->quorum = $quorum;

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection|User[]
     */
    public function getAttendees()
    {
        return $this->attendees;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection|User[] $attendees
     *
     * @return self
     */
    public function setAttendees($attendees)
    {
        $this->attendees = $attendees;

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection|Proxy[]
     */
    public function getProxies()
    {
        return $this->proxies;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection|Proxy[] $proxies
     *
     * @return self
     */
    public function setProxies($proxies)
    {
        $this->proxies = $proxies;

        return $this;
    }
}
