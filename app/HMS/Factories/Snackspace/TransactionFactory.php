<?php

namespace HMS\Factories\Snackspace;

use HMS\Entities\User;
use HMS\Entities\Snackspace\Transaction;

class TransactionFactory
{
    /**
     * Function to instantiate a new Transaction from given params.
     *
     * @param User $user
     * @param int $amount
     * @param string $type
     * @param string $description
     *
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
