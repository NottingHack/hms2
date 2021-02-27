<?php

namespace HMS\Entities\Banking;

class Bank
{
    /**
     * @var int
     */
    protected $id;

    /**
     * Display name in HMS.
     *
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $sortCode;

    /**
     * @var string
     */
    protected $accountNumber;

    /**
     * Name on the account.
     *
     * @var string
     */
    protected $accountName;

    /**
     * @var string
     */
    protected $type;

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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return self
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getSortCode()
    {
        return $this->sortCode;
    }

    /**
     * @param string $sortCode
     *
     * @return self
     */
    public function setSortCode(string $sortCode)
    {
        $this->sortCode = $sortCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getAccountNumber()
    {
        return $this->accountNumber;
    }

    /**
     * @param string $accountNumber
     *
     * @return self
     */
    public function setAccountNumber(string $accountNumber)
    {
        $this->accountNumber = $accountNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getAccountName()
    {
        return $this->accountName;
    }

    /**
     * @param string $accountName
     *
     * @return self
     */
    public function setAccountName(string $accountName)
    {
        $this->accountName = $accountName;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getTypeString()
    {
        return BankType::TYPE_STRINGS[$this->type];
    }

    /**
     * @param string $type
     *
     * @return self
     */
    public function setType(string $type)
    {
        $this->type = $type;

        return $this;
    }
}
