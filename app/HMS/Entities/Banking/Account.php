<?php

namespace HMS\Entities\Banking;

class Account
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $paymentRef;

    /**
     * @var string
     */
    protected $natwestRef;

    /**
     * @var \HMS\Entities\User
     */
    protected $users;

    /**
     * @param string $paymentRef
     */
    public function __construct($paymentRef)
    {
        $this->paymentRef = $paymentRef;
        $this->users = new ArrayCollection();
    }

    /**
     * Gets the value of id.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Gets the value of paymentRef.
     *
     * @return mixed
     */
    public function getPaymentRef()
    {
        return $this->paymentRef;
    }

    /**
     * Sets the value of paymentRef.
     *
     * @param mixed $paymentRef the payment ref
     *
     * @return self
     */
    protected function setPaymentRef($paymentRef)
    {
        $this->paymentRef = $paymentRef;

        return $this;
    }

    /**
     * Gets the value of natwestRef.
     *
     * @return mixed
     */
    public function getNatwestRef()
    {
        return $this->natwestRef;
    }

    /**
     * Gets the value of users.
     *
     * @return \HMS\Entities\User
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Sets the value of users.
     *
     * @param \HMS\Entities\User $users the users
     *
     * @return self
     */
    public function setUsers(\HMS\Entities\User $users)
    {
        $this->users = $users;

        return $this;
    }
}
