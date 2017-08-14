<?php

namespace HMS\Repositories\Banking;

use HMS\Entities\Banking\Account;

interface AccountRepository
{
    /**
     * @param  $id
     * @return null|Account
     */
    public function find($id);

    /**
     * @param  string $paymentRef
     * @return array
     */
    public function findOneByPaymentRef(string $paymentRef);

    /**
     * @param  string $paymentRef
     * @return array
     */
    public function findLikeByPaymentRef(string $paymentRef);

    /**
     * save Account to the DB.
     * @param  Account $account
     */
    public function save(Account $account);
}
