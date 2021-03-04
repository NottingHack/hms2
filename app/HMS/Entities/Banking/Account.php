<?php

namespace HMS\Entities\Banking;

use Doctrine\Common\Collections\ArrayCollection;

class Account
{
    /**
     * Maximum length for a Payment reference.
     */
    const MAX_REFERENCE_LENGHT = 16;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $paymentRef;

    /**
     * @var null|string
     */
    protected $legacyRef;

    /**
     * @var \Doctrine\Common\Collections\Collection|\HMS\Entities\User[]
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
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Gets the value of paymentRef.
     *
     * @return string
     */
    public function getPaymentRef(): string
    {
        return $this->paymentRef;
    }

    /**
     * Sets the value of paymentRef.
     *
     * @param string $paymentRef the payment ref
     *
     * @return self
     */
    protected function setPaymentRef(string $paymentRef): self
    {
        $this->paymentRef = $paymentRef;

        return $this;
    }

    /**
     * Gets the value of legacyRef.
     *
     * @return string|null
     */
    public function getLegacyRef(): ?string
    {
        return $this->legacyRef;
    }

    /**
     * Gets the value of users.
     *
     * @return \Doctrine\Common\Collections\Collection|\HMS\Entities\User[]
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Sets the value of users.
     *
     * @param \Doctrine\Common\Collections\Collection|\HMS\Entities\User[] $users the users
     *
     * @return self
     */
    public function setUsers($users)
    {
        $this->users = $users;

        return $this;
    }
}
