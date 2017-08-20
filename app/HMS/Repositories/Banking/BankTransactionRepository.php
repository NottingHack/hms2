<?php

namespace HMS\Repositories\Banking;

use HMS\Entities\Banking\Account;
use HMS\Entities\Banking\BankTransaction;

interface BankTransactionRepository
{
    /**
     * find the latest transaction for each account.
     * @return array
     */
    public function findLatestTransactionForAllAccounts();

    /**
     * find the latest transaction for given account.
     * @param  Account $account
     * @return null|BankTransaction
     */
    public function findLatestTransactionByAccount(Account $account);

    /**
     * find a matching tranaction in the db or save this one
     * @param  BankTransaction $bankTransaction
     * @return BankTransaction
     */
    public function findOrSave(BankTransaction $bankTransaction);

    /**
     * save BankTransaction to the DB.
     * @param  BankTransaction $bankTransaction
     */
    public function save(BankTransaction $bankTransaction);
}
