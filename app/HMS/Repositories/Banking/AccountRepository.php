<?php

namespace HMS\Repositories\Banking;

use HMS\Entities\Banking\Account;

interface AccountRepository
{
    /**
     * @param int $id
     *
     * @return null|Account
     */
    public function findOneById(int $id);

    /**
     * @return Account[]
     */
    public function findAll();

    /**
     * @param string $paymentRef
     *
     * @return null|Account
     */
    public function findOneByPaymentRef(string $paymentRef);

    /**
     * @param string $paymentRef
     *
     * @return Account[]
     */
    public function findLikeByPaymentRef(string $paymentRef);

    /**
     * @param int $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateAll($perPage = 15, $pageName = 'page');

    /**
     * @param int $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateJointAccounts($perPage = 15, $pageName = 'page');

    /**
     * Save Account to the DB.
     *
     * @param Account $account
     */
    public function save(Account $account);
}
