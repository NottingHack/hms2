<?php

namespace HMS\Entities\Snackspace;

use Carbon\Carbon;
use HMS\Entities\User;

class Email
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var string
     */
    protected $to;

    /**
     * @var string
     */
    protected $cc;

    /**
     * @var string
     */
    protected $bcc;

    /**
     * @var string
     */
    protected $subject;

    /**
     * @var string
     */
    protected $body;

    /**
     * @var string
     */
    protected $bodyAlt;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var Carbon
     */
    protected $date;

    /**
     * @var int
     */
    protected $link;

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
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @return string
     */
    public function getCc()
    {
        return $this->cc;
    }

    /**
     * @return string
     */
    public function getBcc()
    {
        return $this->bcc;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return string
     */
    public function getBodyAlt()
    {
        return $this->bodyAlt;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return Carbon
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return int
     */
    public function getLink()
    {
        return $this->link;
    }
}
