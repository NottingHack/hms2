<?php

namespace HMS\Entities\Snackspace;

class PurchasePayment
{
    /**
     * @var Transaction
     */
    protected $purchase;

    /**
     * @var Transaction
     */
    protected $payment;

    /**
     * @var int
     */
    protected $amount;

    /**
     * @return Transaction
     */
    public function getPurchase()
    {
        return $this->purchase;
    }

    /**
     * @return Transaction
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param Transaction $purchase
     *
     * @return self
     */
    public function setPurchase(Transaction $purchase)
    {
        $this->purchase = $purchase;

        return $this;
    }

    /**
     * @param Transaction $payment
     *
     * @return self
     */
    public function setPayment(Transaction $payment)
    {
        $this->payment = $payment;

        return $this;
    }

    /**
     * @param int $amount
     *
     * @return self
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }
}
