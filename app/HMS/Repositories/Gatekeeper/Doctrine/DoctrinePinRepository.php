<?php

namespace HMS\Repositories\Gatekeeper\Doctrine;

use Doctrine\ORM\EntityRepository;
use HMS\Entities\Gatekeeper\Pin;
use HMS\Entities\User;
use HMS\Repositories\Gatekeeper\PinRepository;

class DoctrinePinRepository extends EntityRepository implements PinRepository
{
    /**
     * @param string $pin
     *
     * @return null|Pin
     */
    public function findOneByPin(string $pin)
    {
        return parent::findOneByPin($pin);
    }

    /**
     * @param User $user
     *
     * @return Pin[]
     */
    public function findByUser(User $user)
    {
        return parent::findByUser($user);
    }

    /**
     * Save Pin to the DB.
     *
     * @param Pin $pin
     */
    public function save(Pin $pin)
    {
        $this->_em->persist($pin);
        $this->_em->flush();
    }
}
