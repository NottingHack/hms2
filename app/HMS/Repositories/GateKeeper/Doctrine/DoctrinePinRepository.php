<?php

namespace HMS\Repositories\GateKeeper\Doctrine;

use HMS\Entities\User;
use HMS\Entities\GateKeeper\Pin;
use Doctrine\ORM\EntityRepository;
use HMS\Repositories\GateKeeper\PinRepository;

class DoctrinePinRepository extends EntityRepository implements PinRepository
{
    /**
     * @param  string $pin
     * @return null|Pin
     */
    public function findOneByPin(string $pin)
    {
        return parent::findOneByPin($pin);
    }

    /**
     * @param  User $user
     * @return Pin[]
     */
    public function findByUser(User $user)
    {
        return parent::findByUser($user);
    }

    /**
     * save Pin to the DB.
     * @param  Pin $pin
     */
    public function save(Pin $pin)
    {
        $this->_em->persist($pin);
        $this->_em->flush();
    }
}
