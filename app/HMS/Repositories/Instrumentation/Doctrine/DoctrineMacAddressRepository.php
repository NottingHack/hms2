<?php

namespace HMS\Repositories\Instrumentation\Doctrine;

use Carbon\Carbon;
use Doctrine\ORM\EntityRepository;
use HMS\Entities\Instrumentation\MacAddress;
use HMS\Repositories\Instrumentation\MacAddressRepository;

class DoctrineMacAddressRepository extends EntityRepository implements MacAddressRepository
{
    /**
     * Count of MacAddresses seen in the last 5 minutes.
     *
     * @return int
     */
    public function countSeenLastFiveMinutes(): int
    {
        $qb = parent::createQueryBuilder('addresses')
            ->select('COUNT(addresses.id)')
            ->where('addresses.lastSeen > :before');

        $qb->setParameter('before', Carbon::now()->subMinutes(5));

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Save MacAddress to the DB.
     *
     * @param MacAddress $macAddress
     */
    public function save(MacAddress $macAddress)
    {
        $this->_em->persist($macAddress);
        $this->_em->flush();
    }
}
