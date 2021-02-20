<?php

namespace HMS\Repositories\Snackspace\Doctrine;

use Carbon\Carbon;
use HMS\Entities\Snackspace\Debt;
use Doctrine\ORM\EntityRepository;
use HMS\Repositories\Snackspace\DebtRepository;

class DoctrineDebtRepository extends EntityRepository implements DebtRepository
{
    /**
     * Finds all entities in the repository.
     *
     * @return Debt[]
     */
    public function findAll()
    {
        return parent::findAll();
    }

    /**
     * Finds all entities in the repository between dates.
     *
     * @param Carbon $start
     * @param Carbon $end
     *
     * @return Debt[]
     */
    public function findBetweeenAuditTimes(Carbon $start, Carbon $end)
    {
        $q = parent::createQueryBuilder('debt');

        $q = $q->where($q->expr()->between('debt.auditTime', ':start', ':end'))
            ->orderBy('debt.auditTime', 'ASC');

        $q = $q->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery();

        return $q->getResult();
    }

    /**
     * Find lastest entry.
     *
     * @return null|Debt
     */
    public function findLatest()
    {
        return parent::findOneBy([], ['auditTime' => 'DESC']);
    }

    /**
     * Save Debt to the DB.
     *
     * @param Debt $debt
     */
    public function save(Debt $debt)
    {
        $this->_em->persist($debt);
        $this->_em->flush();
    }
}
