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
}
