<?php

namespace HMS\Repositories\Banking;

use HMS\Entities\Banking\Account;
use HMS\Entities\Banking\BankTransaction;

interface BankTransactionRepository
{
    /**
     * Find all transactions.
     *
     * @return array
     */
    public function findAll();

    /**
     * Find the latest transaction for each account.
     *
     * @return array
     */
    public function findLatestTransactionForAllAccounts();

    /**
     * Find the latest transaction for given account.
     *
     * @param Account $account
     *
     * @return null|BankTransaction
     */
    public function findLatestTransactionByAccount(Account $account);

    /**
     * Find all transactions for a given account and pagineate them.
     * Ordered by transactionDate DESC.
     *
     * @param null|Account $account
     * @param int $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateByAccount(?Account $account, $perPage = 15, $pageName = 'page');

    /**
     * Find a matching transaction in the db or save this one.
     *
     * @param BankTransaction $bankTransaction
     *
     * @return BankTransaction
     */
    public function findOrSave(BankTransaction $bankTransaction);

    /**
     * Save BankTransaction to the DB.
     *
     * @param BankTransaction $bankTransaction
     */
    public function save(BankTransaction $bankTransaction);
}
