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
     * find all transactions for a given account and pagineate them.
     * Ordered by transactionDate DESC.
     *
     * @param null|Account   $account
     * @param int    $perPage
     * @param string $pageName
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateByAccount(?Account $account, $perPage = 15, $pageName = 'page');

    /**
     * save BankTransaction to the DB.
     * @param  BankTransaction $bankTransaction
     */
    public function save(BankTransaction $bankTransaction);
}
