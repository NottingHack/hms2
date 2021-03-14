<?php

namespace HMS\Repositories\Snackspace;

use Carbon\Carbon;
use HMS\Entities\Snackspace\Transaction;
use HMS\Entities\User;

interface TransactionRepository
{
    /**
     * Generate Payment report between given dates.
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     *
     * @return \Illuminate\Support\Collection
     */
    public function reportPaymnetsBetween(Carbon $startDate, Carbon $endDate);

    /**
     * Find all transactions for a given user and pagineate them.
     * Ordered by transactionDatetime DESC.
     *
     * @param User $user
     * @param int $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateByUser(User $user, $perPage = 15, $pageName = 'page');

    /**
     * Save Transaction to the DB and update the users balance.
     *
     * @param Transaction $transaction
     *
     * @return Transaction
     */
    public function saveAndUpdateBalance(Transaction $transaction);
}
