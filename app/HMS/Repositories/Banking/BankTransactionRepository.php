<?php

namespace HMS\Repositories\Banking;

use HMS\Entities\Banking\BankTransaction;

interface BankTransactionRepository
{
    /**
     * save BankTransaction to the DB.
     * @param  BankTransaction $bankTransaction
     */
    public function save(BankTransaction $bankTransaction);
}
