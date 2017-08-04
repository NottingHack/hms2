<?php

namespace HMS\Factories\Banking;

use Carbon\Carbon;
use HMS\Entities\Banking\Bank;
use HMS\Entities\Banking\BankTransaction;
use HMS\Repositories\Banking\AccountRepository;
use HMS\Repositories\Banking\BankTransactionRepository;

class BankTransactionFactory
{
    /**
     * @var BankTransactionRepository
     */
    protected $bankTransactionRepository;
    protected $accountRepository;

    /**
     * @param BankTransactionRepository $bankTransactionRepository
     */
    public function __construct(BankTransactionRepository $bankTransactionRepository, AccountRepository $accountRepository)
    {
        $this->bankTransactionRepository = $bankTransactionRepository;
        $this->accountRepository = $accountRepository;
    }

    /**
     * Function to instantiate a new BankTransaction from given params.
     * @param Bank $bank
     * @param Carbon $transactionDate
     */
    public function create(Bank $bank, Carbon $transactionDate, string $description, float $amount)
    {
        $_bankTransaction = new BankTransaction();
        $_bankTransaction->setBank($bank);
        $_bankTransaction->setTransactionDate($transactionDate);
        $_bankTransaction->setDescription($description);
        $_bankTransaction->setAmount($amount);

        if (preg_match('/HSNTSB\S{10}(?= )/', $description, $matches) == 1) {
            $account = $this->accountRepository->findOneByPaymentRef($matches[0]);
            $_bankTransaction->setAccount($account);
        }

        return $_bankTransaction;
    }
}
