<?php

namespace HMS\Repositories\Snackspace;

use HMS\Entities\User;
use HMS\Entities\Snackspace\Transaction;

interface TransactionRepository
{
    /**
     * find all transactions for a given user and pagineate them.
     * Ordered by transactionDatetime DESC.
     *
     * @param User   $user
     * @param int    $perPage
     * @param string $pageName
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateByUser(User $user, $perPage = 15, $pageName = 'page');

    /**
     * save Transaction to the DB and update the users balance.
     * @param  Transaction $transaction
     */
    public function saveAndUpdateBalance(Transaction $transaction);

    /**
     * save Transaction to the DB.
     * @param  Transaction $transaction
     */
    public function save(Transaction $transaction);
}
