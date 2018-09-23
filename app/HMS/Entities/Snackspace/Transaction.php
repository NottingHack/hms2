<?php

namespace HMS\Entities\Snackspace;

use Carbon\Carbon;
use HMS\Entities\User;

class Transaction
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
    protected $transactionDatetime;

    /**
     * @var null|int
     */
    protected $amount;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var null|string
     */
    protected $description;

    /**
     * @var Product
     */
    protected $product;

    /**
     * @var null|User
     */
    protected $recordedUser;

    /**
     * Create a new transaction as User and ammount can not be changes later.
     *
     * @param User   $user
     * @param int    $amount
     * @param string $status
     */
    public function __construct(User $user, int $amount, string $status = TransactionState::PENDING)
    {
        $this->user = $user;
        $this->amount = $amount;
        $this->status = $status;
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
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return Carbon
     */
    public function getTransactionDatetime()
    {
        return $this->transactionDatetime;
    }

    /**
     * @param Carbon $transactionDatetime
     *
     * @return self
     */
    public function setTransactionDatetime(Carbon $transactionDatetime)
    {
        $this->transactionDatetime = $transactionDatetime;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     *
     * @return self
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param null|string $description
     *
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param Product $product
     *
     * @return self
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return null|User
     */
    public function getRecordedUser()
    {
        return $this->recordedUser;
    }

    /**
     * @param null|User $recordedUser
     *
     * @return self
     */
    public function setRecordedUser($recordedUser)
    {
        $this->recordedUser = $recordedUser;

        return $this;
    }
}
