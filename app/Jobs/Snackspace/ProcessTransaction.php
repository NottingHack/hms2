<?php

namespace App\Jobs\Snackspace;

use HMS\Entities\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use HMS\Entities\Snackspace\Transaction;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use HMS\Entities\Snackspace\PurchasePayment;
use HMS\Repositories\Snackspace\TransactionRepository;
use HMS\Repositories\Snackspace\PurchasePaymentRepository;

class ProcessTransaction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Transaction
     */
    protected $transaction;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var PurchasePaymentRepository
     */
    protected $purchasePaymentRepository;

    /**
     * Create a new job instance.
     *
     * @param Transaction $transaction
     *
     * @return void
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Execute the job.
     * This is the php equivalent of sp_process_transaction().
     *
     * @param TransactionRepository $transactionRepository
     * @param PurchasePaymentRepository $purchasePaymentRepository
     *
     * @return void
     */
    public function handle(
        TransactionRepository $transactionRepository,
        PurchasePaymentRepository $purchasePaymentRepository
    ) {
        $this->purchasePaymentRepository = $purchasePaymentRepository;

        // refresh the transaction
        $this->transaction = $transactionRepository->find($this->transaction->getID());
        $this->user = $this->transaction->getUser();

        if ($this->transaction->getAmount() == 0) {
            return;
        }

        if ($this->transaction->getAmount() < 0) {
            // This is a purchase
            // Check if any (get amount) payments have already been allocated to this transaction
            $alreadyAlloc = $this->purchasePaymentRepository->sumAllocated($this->transaction);

            // there has already been enough payments allocated to cover the purchase, so exit
            if ($alreadyAlloc <= $this->transaction->getAmount()) {
                return;
            }

            // if part of the purchase has already been covered, only try and allocate a payment for the part that hasn't been.
            $amountToAlloc = $this->transaction->getAmount() - $alreadyAlloc;

            // sum unallocated payments
            $unallocPaymentsAmount = $this->purchasePaymentRepository->sumUnallocatedPayments(
                $this->user,
                $this->transaction
            );

            if ($unallocPaymentsAmount > 0) {
                // call sp_process_transaction_alloc_payment(p_transaction_id, v_member_id, -1*v_amount, p_err);
                $this->allocatePayment(-1 * $amountToAlloc);
            }
        } else {
            // This is a payment
            // Check if any this payment has already been fully allocated to purchases
            $alreadyAlloc = $this->purchasePaymentRepository->sumAllocated($this->transaction);

            if ($alreadyAlloc >= $this->transaction->getAmount()) {
                // payment already fully allocated, so exit
                return;
            }

            // if part of the payment has already been allocated, only try and allocate the part that hasn't been.
            $amountToAlloc = $this->transaction->getAmount() - $alreadyAlloc;

            // sum purchases which haven't been (fully) allocated payment
            $unallocPurchasesAmount = $this->purchasePaymentRepository->sumUnallocatedPurchases(
                $this->user,
                $this->transaction
            );

            if ($unallocPurchasesAmount > 0) {
                // call sp_process_transaction_alloc_purchase(p_transaction_id, v_member_id, v_amount, p_err);
                $this->allocatePurchase($amountToAlloc);
            }
        }
    }

    /**
     * Allocate paymets up to amount.
     *
     * @param int $amount
     *
     * @return void
     */
    protected function allocatePayment(int $amount)
    {
        $unallocatedPayments = $this->purchasePaymentRepository->findUnallocatedPayments(
            $this->user,
            $this->transaction
        );

        $amountAllocated = 0;

        foreach ($unallocatedPayments as $payment) {
            if ($amountAllocated >= $amount) {
                return;
            }
            $transactionAmount = $payment['transaction']->getAmount() - $payment['amountAllocated'];

            if ($transactionAmount > ($amount - $amountAllocated)) {
                $amountToAlloc = $amount - $amountAllocated;
            } else {
                $amountToAlloc = $transactionAmount;
            }

            $purchasePayment = new PurchasePayment();
            $purchasePayment->setPurchase($this->transaction);
            $purchasePayment->setPayment($payment['transaction']);
            $purchasePayment->setAmount($amountToAlloc);
            $this->purchasePaymentRepository->save($purchasePayment);

            $amountAllocated += $amountToAlloc;
        }
    }

    /**
     * Allocate purchases up to amount.
     *
     * @param int $amount
     *
     * @return void
     */
    protected function allocatePurchase(int $amount)
    {
        $unallocatedPurchases = $this->purchasePaymentRepository->findUnallocatedPurchases(
            $this->user,
            $this->transaction
        );
        $amountAllocated = 0;

        foreach ($unallocatedPurchases as $purchase) {
            if ($amountAllocated >= $amount) {
                return;
            }
            $transactionAmount = (-1 * $purchase['transaction']->getAmount()) - $purchase['amountAllocated'];

            if ($transactionAmount > ($amount - $amountAllocated)) {
                $amountToAlloc = $amount - $amountAllocated;
            } else {
                $amountToAlloc = $transactionAmount;
            }

            $purchasePayment = new PurchasePayment();
            $purchasePayment->setPurchase($purchase['transaction']);
            $purchasePayment->setPayment($this->transaction);
            $purchasePayment->setAmount($amountToAlloc);
            $this->purchasePaymentRepository->save($purchasePayment);

            $amountAllocated += $amountToAlloc;
        }
    }
}
