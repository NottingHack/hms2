<?php

namespace HMS\Factories\Banking;

use HMS\Entities\Banking\Bank;
use HMS\Repositories\Banking\BankRepository;

class BankFactory
{
    /**
     * @var BankRepository
     */
    protected $bankRepository;

    /**
     * @param BankRepository $bankRepository
     */
    public function __construct(BankRepository $bankRepository)
    {
        $this->bankRepository = $bankRepository;
    }

    /**
     * Function to instantiate a new BankFactory from given params.
     *
     * @param string $name
     * @param string $sortCode
     * @param string $accountNumber
     * @param string $accountName
     * @param string $type
     *
     * @return Bank
     */
    public function create(
        string $name,
        string $sortCode,
        string $accountNumber,
        string $accountName,
        string $type
    ) {
        $_bank = new Bank();

        $_bank->setName($name);
        $_bank->setSortCode($sortCode);
        $_bank->setAccountNumber($accountNumber);
        $_bank->setAccountName($accountName);
        $_bank->setType($type);

        return $_bank;
    }
}
