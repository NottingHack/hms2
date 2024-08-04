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
     * @param boot $filterIgnores Default true
     *
     * @return int
     */
    public function countSeenLastFiveMinutes(bool $filterIgnores = true): int
    {
        $qb = parent::createQueryBuilder('addresses');

        $expr = $qb->expr();
        $qb->select('COUNT(addresses.id)')
            ->where($expr->gt('addresses.lastSeen', ':before'));

        if ($filterIgnores) {
            $qb->andWhere($expr->eq('addresses.ignore', 0));
        }

        $qb->setParameter('before', Carbon::now()->subMinutes(5));

        ray($qb->getQuery()->getSql());
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
