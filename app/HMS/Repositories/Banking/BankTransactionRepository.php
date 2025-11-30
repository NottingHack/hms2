<?php

namespace HMS\Repositories\Banking;

use Carbon\Carbon;
use HMS\Entities\Banking\Account;
use HMS\Entities\Banking\Bank;
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
     * Find One By TransactionDate And Description And Amount.
     *
     * @param Bank $bank
     * @param Carbon $date
     * @param string $description
     * @param int $amount
     *
     * @return null|BankTransaction
     */
    public function findOneByBankAndDateAndDescriptionAndAmount(
        Bank $bank,
        Carbon $date,
        string $description,
        int $amount
    );

    /**
     * Find the latest transaction for each account.
     *
     * @return array
     */
    public function findLatestTransactionForAllAccounts();

    /**
     * Find the latest transaction.
     *
     * @return array
     */
    public function findLatestTransaction();

    /**
     * Find the latest transaction for given account.
     *
     * @param Account $account
     *
     * @return null|BankTransaction
     */
    public function findLatestTransactionByAccount(Account $account);

    /**
     * Find the latest transaction for given account and greater than or equal to amount.
     *
     * @param Account $account
     * @param int $amount
     *
     * @return null|BankTransaction
     */
    public function findLatestTransactionByAccountGTeAmount(Account $account, int $amount);

    /**
     * Find the latest transaction for given Bank.
     *
     * @param Bank $bank
     *
     * @return null|BankTransaction
     */
    public function findLatestTransactionByBank(Bank $bank);

    /**
     * Find all unmatched transactions and paginate them.
     * Ordered by transactionDate DESC.
     *
     * @param int $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateUnmatched($perPage = 15, $pageName = 'page');

    /**
     * Find all transactions for a given Account and paginate them.
     * Ordered by transactionDate DESC.
     *
     * @param Account $account
     * @param int $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateByAccount(Account $account, $perPage = 15, $pageName = 'page');

    /**
     * Find all transactions for a given Bank and paginate them.
     * Ordered by transactionDate DESC.
     *
     * @param Bank $bank
     * @param int $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateByBank(Bank $bank, $perPage = 15, $pageName = 'page');

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
