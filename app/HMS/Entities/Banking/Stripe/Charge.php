<?php

namespace HMS\Entities\Banking\Stripe;

use HMS\Entities\User;
use HMS\Traits\Entities\Timestampable;
use HMS\Entities\Snackspace\Transaction;

class Charge
{
    use Timestampable;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var null|string
     */
    protected $paymentIntentId;

    /**
     * @var null|string
     */
    protected $refundId;

    /**
     * @var null|string
     */
    protected $disputeId;

    /**
     * @var null|User
     */
    protected $user;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var int
     */
    protected $amount;

    /**
     * @var null|Transaction
     */
    protected $transaction;

    /**
     * @var null|Transaction
     */
    protected $refundTransaction;

    /**
     * @var null|Transaction
     */
    protected $withdrawnTransaction;

    /**
     * @var null|Transaction
     */
    protected $reinstatedTransaction;

    /**
     * Gets the value of id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPaymentIntentId()
    {
        return $this->paymentIntentId;
    }

    /**
     * @param null|string $paymentIntentId
     *
     * @return self
     */
    public function setPaymentIntentId($paymentIntentId)
    {
        $this->paymentIntentId = $paymentIntentId;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getRefundId()
    {
        return $this->refundId;
    }

    /**
     * @param null|string $refundId
     *
     * @return self
     */
    public function setRefundId($refundId)
    {
        $this->refundId = $refundId;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDisputeId()
    {
        return $this->disputeId;
    }

    /**
     * @param null|string $disputeId
     *
     * @return self
     */
    public function setDisputeId($disputeId)
    {
        $this->disputeId = $disputeId;

        return $this;
    }

    /**
     * @return null|User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param null|User $user
     *
     * @return self
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getTypeString()
    {
        return ChargeType::TYPE_STRINGS[$this->type];
    }

    /**
     * @param string $type
     *
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
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

    /**
     * @return null|Transaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * @param null|Transaction $transaction
     *
     * @return self
     */
    public function setTransaction($transaction)
    {
        $this->transaction = $transaction;

        return $this;
    }

    /**
     * @return null|Transaction
     */
    public function getRefundTransaction()
    {
        return $this->refundTransaction;
    }

    /**
     * @param null|Transaction $refundTransaction
     *
     * @return self
     */
    public function setRefundTransaction($refundTransaction)
    {
        $this->refundTransaction = $refundTransaction;

        return $this;
    }

    /**
     * @return null|Transaction
     */
    public function getWithdrawnTransaction()
    {
        return $this->withdrawnTransaction;
    }

    /**
     * @param null|Transaction $withdrawnTransaction
     *
     * @return self
     */
    public function setWithdrawnTransaction($withdrawnTransaction)
    {
        $this->withdrawnTransaction = $withdrawnTransaction;

        return $this;
    }

    /**
     * @return null|Transaction
     */
    public function getReinstatedTransaction()
    {
        return $this->reinstatedTransaction;
    }

    /**
     * @param null|Transaction $reinstatedTransaction
     *
     * @return self
     */
    public function setReinstatedTransaction($reinstatedTransaction)
    {
        $this->reinstatedTransaction = $reinstatedTransaction;

        return $this;
    }
}
