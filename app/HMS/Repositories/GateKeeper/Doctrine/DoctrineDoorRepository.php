<?php

namespace HMS\Repositories\GateKeeper\Doctrine;

use HMS\Entities\GateKeeper\Door;
use Doctrine\ORM\EntityRepository;
use HMS\Repositories\GateKeeper\DoorRepository;

class DoctrineDoorRepository extends EntityRepository implements DoorRepository
{
    /**
     * Find by short name.
     *
     * @param string $shortName
     *
     * @return null|Door
     */
    public function findOneByShortName(string $shortName)
    {
        return parent::findOneByShortName($shortName);
    }

    /**
     * Save Door to the DB.
     *
     * @param Door $door
     */
    public function save(Door $door)
    {
        $this->_em->persist($door);
        $this->_em->flush();
    }
}
