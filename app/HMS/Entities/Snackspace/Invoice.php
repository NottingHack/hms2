<?php

namespace HMS\Entities\Snackspace;

use Carbon\Carbon;
use HMS\Entities\User;

class Invoice
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
     * @var Carbon
     */
    protected $from;

    /**
     * @var Carbon
     */
    protected $to;

    /**
     * @var Carbon
     */
    protected $generated;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var int
     */
    protected $amount;

    /**
     * @var Email
     */
    protected $email;

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
     * @return Carbon
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @return Carbon
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @return Carbon
     */
    public function getGenerated()
    {
        return $this->generated;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return Email
     */
    public function getEmail()
    {
        return $this->email;
    }
}
