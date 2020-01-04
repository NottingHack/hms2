<?php

namespace HMS\Repositories\Tools\Doctrine;

use Carbon\Carbon;
use HMS\Entities\Tools\Tool;
use HMS\Entities\Tools\Usage;
use Doctrine\ORM\EntityRepository;
use HMS\Repositories\Tools\UsageRepository;

class DoctrineUsageRepository extends EntityRepository implements UsageRepository
{
    /**
     * @param Tool $tool
     * @param Carbon $start
     * @param Carbon $end
     *
     * @return Usage[]
     */
    public function findByToolBetween(Tool $tool, Carbon $start, Carbon $end)
    {
        $q = parent::createQueryBuilder('usage');

        $q = $q->where($q->expr()->between('usage.start', ':start', ':end'))
            ->andWhere('usage.tool = :tool_id')
            ->orderBy('usage.start', 'ASC');

        $q = $q->setParameter('tool_id', $tool->getId())
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery();

        return $q->getResult();
    }

    /**
     * @param Tool $tool
     * @param Carbon $day
     *
     * @return Usage[]
     */
    public function findByToolForDay(Tool $tool, Carbon $day)
    {
        $start = $day->copy()->startOfDay();
        $end = $day->copy()->endOfDay();

        return $this->findByToolBetween($tool, $start, $end);
    }

    /**
     * @param Tool $tool
     * @param Carbon $week
     *
     * @return Usage[]
     */
    public function findByToolForWeek(Tool $tool, Carbon $week)
    {
        $start = $week->copy()->startOfWeek();
        $end = $week->copy()->endOfWeek();

        return $this->findByToolBetween($tool, $start, $end);
    }

    /**
     * @param Tool $tool
     * @param Carbon $month
     *
     * @return Usage[]
     */
    public function findByToolForMonth(Tool $tool, Carbon $month)
    {
        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();

        return $this->findByToolBetween($tool, $start, $end);
    }

    /**
     * @param Tool $tool
     *
     * @return Usage[]
     */
    public function findByToolForThisWeek(Tool $tool)
    {
        $week = Carbon::now('Europe/London');

        return $this->findByToolForWeek($tool, $week);
    }

    /**
     * Save Usage to the DB.
     *
     * @param Usage $usage
     */
    public function save(Usage $usage)
    {
        $this->_em->persist($usage);
        $this->_em->flush();
    }
}
