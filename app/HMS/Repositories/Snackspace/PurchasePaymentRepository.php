<?php

namespace HMS\Repositories\Snackspace;

use HMS\Entities\Snackspace\PurchasePayment;
use HMS\Entities\Snackspace\Transaction;
use HMS\Entities\User;

interface PurchasePaymentRepository
{
    /**
     * Sum how much of the current transation has been allocated.
     *
     * @param Transaction $transaction
     *
     * @return int
     */
    public function sumAllocated(Transaction $transaction);

    /**
     * Sum unallocated payments for User before Transaction.
     *
     * @param User $user
     * @param Transaction $transaction
     *
     * @return int
     */
    public function sumUnallocatedPayments(User $user, Transaction $transaction);

    /**
     * Sum unallocated purchases for User before Transaction.
     *
     * @param User $user
     * @param Transaction $transaction
     *
     * @return int
     */
    public function sumUnallocatedPurchases(User $user, Transaction $transaction);

    /**
     * Find unallocated payments for User before Transaction.
     *
     * @param User $user
     * @param Transaction $transaction
     *
     * @return array
     */
    public function findUnallocatedPayments(User $user, Transaction $transaction);

    /**
     * Find unallocated purchases for User before Transaction.
     *
     * @param User $user
     * @param Transaction $transaction
     *
     * @return array
     */
    public function findUnallocatedPurchases(User $user, Transaction $transaction);

    /**
     * Save PurchasePayment to the DB.
     *
     * @param PurchasePayment $purchasePayment
     */
    public function save(PurchasePayment $purchasePayment);
}
