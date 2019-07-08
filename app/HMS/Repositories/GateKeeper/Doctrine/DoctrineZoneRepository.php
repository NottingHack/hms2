<?php

namespace HMS\Repositories\GateKeeper\Doctrine;

use HMS\Entities\GateKeeper\Zone;
use Doctrine\ORM\EntityRepository;
use HMS\Repositories\GateKeeper\ZoneRepository;

class DoctrineZoneRepository extends EntityRepository implements ZoneRepository
{
    /**
     * Find all zones.
     *
     * @return Zone[]
     */
    public function findAll()
    {
        return parent::findAll();
    }

    /**
     * Find one zone by short name
     *
     * @param string $shortName
     *
     * @return Zone
     */
    public function findOneByShortName(string $shortName)
    {
        return parent::findOneByShortName($shortName);
    }

    /**
     * Save Zone to the DB.
     *
     * @param Zone $zone
     */
    public function save(Zone $zone)
    {
        $this->_em->persist($zone);
        $this->_em->flush();
    }
}
