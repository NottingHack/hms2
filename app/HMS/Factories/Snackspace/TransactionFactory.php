<?php

namespace HMS\Factories\Snackspace;

use HMS\Entities\User;
use HMS\Entities\Snackspace\Transaction;
use HMS\Repositories\Snackspace\TransactionRepository;

class TransactionFactory
{
    /**
     * @var TransactionRepository
     */
    protected $transactionRepository;

    /**
     * @param TransactionRepository $transactionRepository
     */
    public function __construct(TransactionRepository $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * Function to instantiate a new Transaction from given params.
     *
     * @param  User   $user
     * @param  int    $amount
     * @param  string $type
     * @param  string $description
     * @return Transaction
     */
    public function create(User $user, int $amount, string $type, string $description)
    {
        $_transaction = new Transaction($user, $amount);
        $_transaction->setType($type);
        $_transaction->setDescription($description);
        if ($user != \Auth::user()) {
            $_transaction->setRecordedUser(\Auth::user());
        }

        return $_transaction;
    }
}
