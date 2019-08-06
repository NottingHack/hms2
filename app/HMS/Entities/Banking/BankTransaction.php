<?php

namespace HMS\Entities\Banking;

use Carbon\Carbon;
use HMS\Entities\Snackspace\Transaction;

class BankTransaction
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var Carbon
     */
    protected $transactionDate;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var int
     */
    protected $amount;

    /**
     * @var Bank
     */
    protected $bank;

    /**
     * @var null|Account
     */
    protected $account;

    /**
     * @var null|Transaction
     */
    protected $transaction;

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
     * @return Carbon
     */
    public function getTransactionDate()
    {
        return $this->transactionDate;
    }

    /**
     * @param Carbon $transactionDate
     *
     * @return self
     */
    public function setTransactionDate(Carbon $transactionDate)
    {
        $this->transactionDate = $transactionDate;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param int $amount
     *
     * @return self
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return Bank
     */
    public function getBank()
    {
        return $this->bank;
    }

    /**
     * @param Bank $bank
     *
     * @return self
     */
    public function setBank(Bank $bank)
    {
        $this->bank = $bank;

        return $this;
    }

    /**
     * @return null|Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @param null|Account $account
     *
     * @return self
     */
    public function setAccount(?Account $account)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * @return null|Transaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * @param null|Transaction $transaction
     *
     * @return self
     */
    public function setTransaction($transaction)
    {
        $this->transaction = $transaction;

        return $this;
    }
}
