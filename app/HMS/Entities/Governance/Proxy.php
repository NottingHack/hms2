<?php

namespace HMS\Entities\Governance;

use HMS\Entities\User;
use HMS\Traits\Entities\Timestampable;

class Proxy
{
    use Timestampable;

    /**
     * @var int
     */
    protected $id;

    /**
     * Meeting this proxy is for.
     *
     * @var Meeting
     */
    protected $meeting;

    /**
     * User designating a proxy.
     *
     * @var User
     */
    protected $principal;

    /**
     * User this proxy has been designated to.
     *
     * @var User
     */
    protected $proxy;

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
     * @return Meeting
     */
    public function getMeeting()
    {
        return $this->meeting;
    }

    /**
     * @param Meeting $meeting
     *
     * @return self
     */
    public function setMeeting(Meeting $meeting)
    {
        $this->meeting = $meeting;

        return $this;
    }

    /**
     * @return User
     */
    public function getPrincipal()
    {
        return $this->principal;
    }

    /**
     * @param User $principal
     *
     * @return self
     */
    public function setPrincipal(User $principal)
    {
        $this->principal = $principal;

        return $this;
    }

    /**
     * @return User
     */
    public function getProxy()
    {
        return $this->proxy;
    }

    /**
     * @param User $proxy
     *
     * @return self
     */
    public function setProxy(User $proxy)
    {
        $this->proxy = $proxy;

        return $this;
    }
}
