<?php

namespace HMS\Repositories\Banking\Stripe\Doctrine;

use Doctrine\ORM\EntityRepository;
use HMS\Entities\Banking\Stripe\Charge;
use HMS\Repositories\Banking\Stripe\ChargeRepository;

class DoctrineChargeRepository extends EntityRepository implements ChargeRepository
{
    /**
     * Find a Charge by id.
     *
     * @param string $id
     *
     * @return null|Charge
     */
    public function findOneById(string $id)
    {
        return parent::findOneById($id);
    }

    /**
     * Save Charge to the DB.
     *
     * @param Charge $charge
     *
     * @return Charge
     */
    public function save(Charge $charge)
    {
        $this->_em->persist($charge);
        $this->_em->flush();

        return $charge;
    }
}
