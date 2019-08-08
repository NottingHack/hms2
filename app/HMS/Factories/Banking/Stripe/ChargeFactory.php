<?php

namespace HMS\Factories\Banking\Stripe;

use HMS\Entities\Banking\Stripe\Charge;
use HMS\Repositories\Banking\Stripe\ChargeRepository;

class ChargeFactory
{
    /**
     * @var ChargeRepository
     */
    protected $chargeRepository;

    /**
     * @param ChargeRepository $chargeRepository
     */
    public function __construct(ChargeRepository $chargeRepository)
    {
        $this->chargeRepository = $chargeRepository;
    }

    /**
     * Function to instantiate a new Charge from given params.
     *
     * @param string $chargeId
     * @param int $amount
     * @param string $type
     *
     * @return Charge
     */
    public function create(string $chargeId, int $amount, string $type)
    {
        $_charge = new Charge();
        $_charge->setId($chargeId);
        $_charge->setAmount($amount);
        $_charge->setType($type);

        return $_charge;
    }
}
