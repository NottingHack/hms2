<?php

namespace HMS\Repositories\Banking\Stripe;

use HMS\Entities\Banking\Stripe\Charge;

interface ChargeRepository
{
    /**
     * Find a Charge by id.
     *
     * @param string id
     *
     * @return null|Charge
     */
    public function findOneById(string $id);

    /**
     * Save Charge to the DB.
     *
     * @param Charge $charge
     *
     * @return Charge
     */
    public function save(Charge $charge);
}
