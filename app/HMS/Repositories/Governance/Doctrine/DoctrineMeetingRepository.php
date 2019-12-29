<?php

namespace HMS\Repositories\Governance\Doctrine;

use Carbon\Carbon;
use Doctrine\ORM\EntityRepository;
use HMS\Entities\Governance\Meeting;
use Doctrine\Common\Collections\Criteria;
use HMS\Repositories\Governance\MeetingRepository;
use LaravelDoctrine\ORM\Pagination\PaginatesFromRequest;

class DoctrineMeetingRepository extends EntityRepository implements MeetingRepository
{
    use PaginatesFromRequest;

    /**
     * Is there a Meeting in the future.
     *
     * @return bool
     */
    public function hasUpcomming()
    {
        $now = Carbon::now();

        $expr = Criteria::expr();
        $criteria = Criteria::create()
            ->where($expr->gte('startTime', $now));

        $results = $this->matching($criteria);

        return ! $results->isEmpty();
    }

    /**
     * Find the next meeting.
     *
     * @return Meeting|null
     */
    public function findNext()
    {
        $now = Carbon::now();

        $expr = Criteria::expr();
        $criteria = Criteria::create()
            ->where($expr->gte('startTime', $now))
            ->orderBy(['startTime' => Criteria::ASC])
            ->setMaxResults(1);

        $results = $this->matching($criteria);

        return $results->isEmpty() ? null : $results->first();
    }

    /**
     * Find all future meetings.
     *
     * @return Meeting[]
     */
    public function findFuture()
    {
        $now = Carbon::now();

        $expr = Criteria::expr();
        $criteria = Criteria::create()
            ->where($expr->gte('startTime', $now));

        return $this->matching($criteria)->toArray();
    }

    /**
     * Finds all meetings in the repository.
     *
     * @return Meeting[]
     */
    public function findAll()
    {
        return parent::findBy([], ['startTime' => 'DESC']);
    }

    /**
     * Save Meeting to the DB.
     *
     * @param Meeting $meeting
     */
    public function save(Meeting $meeting)
    {
        $this->_em->persist($meeting);
        $this->_em->flush();
    }

    /**
     * @param int    $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateAll($perPage = 15, $pageName = 'page')
    {
        $query = $this->createQueryBuilder('o')->orderBy('o.startTime', 'DESC')->getQuery();

        return $this->paginate($query, $perPage, $pageName, false);
    }
}
