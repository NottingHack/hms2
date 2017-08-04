<?php

namespace HMS\Entities\Banking;

use Carbon\Carbon;

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
     * @var float
     */
    protected $amount;

    /**
     * @var Bank
     */
    protected $bank;

    /**
     * @var Account
     */
    protected $account;

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
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $amount
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
     * @return Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @param Account $account
     *
     * @return self
     */
    public function setAccount(Account $account)
    {
        $this->account = $account;

        return $this;
    }
}
