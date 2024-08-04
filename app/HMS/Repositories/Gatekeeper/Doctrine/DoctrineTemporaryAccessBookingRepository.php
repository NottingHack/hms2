<?php

namespace HMS\Repositories\Gatekeeper\Doctrine;

use Carbon\Carbon;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use HMS\Entities\Gatekeeper\BookableArea;
use HMS\Entities\Gatekeeper\Building;
use HMS\Entities\Gatekeeper\TemporaryAccessBooking;
use HMS\Entities\User;
use HMS\Repositories\Gatekeeper\TemporaryAccessBookingRepository;

class DoctrineTemporaryAccessBookingRepository extends EntityRepository implements TemporaryAccessBookingRepository
{
    /**
     * @param int $id
     *
     * @return null|TemporaryAccessBooking
     */
    public function findOneById(int $id)
    {
        return parent::findOneById($id);
    }

    /**
     * Count future bookings for a User on a given Building.
     *
     * @param Building $building
     * @param User $user
     * @param TemporaryAccessBooking|null $ignoreBooking
     *
     * @return int
     */
    public function countFutureForBuildingAndUser(
        Building $building,
        User $user,
        TemporaryAccessBooking $ignoreBooking = null
    ): int {
        $now = Carbon::now();
        // can not use Criteria cause of the join :(
        $qb = parent::createQueryBuilder('temporaryAccessBooking');
        $expr = $qb->expr();

        $qb->select('COUNT(temporaryAccessBooking.id)')
            ->innerJoin('temporaryAccessBooking.bookableArea', 'bookableArea')
            ->addCriteria($this->byUser($user))
            ->andWhere(
                $expr->andX(
                    $expr->eq('bookableArea.building', ':building'),
                    $expr->gte('temporaryAccessBooking.end', ':now')
                )
            );

        if ($ignoreBooking) {
            $qb->andWhere('temporaryAccessBooking != :ignoreBooking')
                ->setParameter('ignoreBooking', $ignoreBooking);
        }

        $qb->setParameter('building', $building)
            ->setParameter('now', $now);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Count future bookings for a User grouped by Building id's.
     *
     * @param User $user
     *
     * @return array
     */
    public function countFutureForUserByBuildings(User $user)
    {
        $now = Carbon::now();
        $qb = parent::createQueryBuilder('temporaryAccessBooking');
        $expr = $qb->expr();

        $subQuery = $qb->select('COUNT(temporaryAccessBooking.id)')
            ->innerJoin('temporaryAccessBooking.bookableArea', 'bookableArea')
            ->addCriteria($this->byUser($user))
            ->andwhere('bookableArea.building = b.id') // not an expr as we dont want to bind a param
            ->andWhere($expr->gte('temporaryAccessBooking.end', ':now'))
            ->getDQL();

        $qb1 = $this->_em->createQueryBuilder();
        $qb1->select('b AS building')
            ->addSelect('(' . $subQuery . ') AS booking_count')
            ->from(Building::class, 'b');

        $qb1->setParameter('user', $user) // lost :user when getDQL on the sub query
            ->setParameter('now', $now);

        $results = $qb1->getQuery()->getResult();

        $results = collect($results)->mapWithKeys(function ($item) {
            return [$item['building']->getId() => (int) $item['booking_count']];
        });

        return $results->all();
    }

    /**
     * Find the latest booking for a User grouped by Building id's.
     *
     * @param User     $user
     *
     * @return array
     */
    public function latestBookingForUserByBuildings(User $user)
    {
        $qb1 = parent::createQueryBuilder('tab');

        $qb1->select('b.id AS building_id')
            ->addSelect('MAX(tab.end) AS end')
            ->leftJoin('tab.bookableArea', 'ba')
            ->leftJoin('ba.building', 'b')
            ->where('tab.user = :user')
            ->andWhere('b IS NOT NULL')
            ->groupBy('ba.building');

        $qb1->setParameter('user', $user);

        $resultsEnd = $qb1->getQuery()->getResult();
        $results = [];

        foreach ($resultsEnd as $end) {
            $qb2 = parent::createQueryBuilder('tab');

            $qb2->leftJoin('tab.bookableArea', 'ba')
                ->leftJoin('ba.building', 'b')
                ->where('tab.user = :user')
                ->andWhere('b.id = :building_id')
                ->andWhere('tab.end = :end')
                ->setMaxResults(1);

            $qb2->setParameter('user', $user)
                ->setParameter('building_id', $end['building_id'])
                ->setParameter('end', $end['end']);

            $booking = $qb2->getQuery()->getSingleResult();
            $results[$end['building_id']] = $booking;
        }

        return $results;
    }

    /**
     * Find the latest booking for a User on a given building that has a start before the given time.
     *
     * @param Carbon   $before
     * @param Building $building
     * @param User     $user
     *
     * @return null|TemporaryAccessBooking
     */
    public function latestBeforeDatetimeForBuildingAndUser(Carbon $before, Building $building, User $user)
    {
        $qb = parent::createQueryBuilder('temporaryAccessBooking');

        $expr = $qb->expr();
        $qb->innerJoin('temporaryAccessBooking.bookableArea', 'bookableArea')
            ->addCriteria($this->byUser($user))
            ->andWhere($expr->lt('temporaryAccessBooking.start', ':before'))
            ->andWhere($expr->eq('bookableArea.building', ':building'))
            ->orderBy('temporaryAccessBooking.end', Criteria::DESC)
            ->setMaxResults(1);

        $q = $qb->setParameter('building', $building)
            ->setParameter('before', $before)
            ->getQuery();

        return $q->getOneOrNullResult();
    }

    /**
     * @param Carbon $start
     * @param Carbon $end
     *
     * @return TemporaryAccessBooking[]
     */
    public function findBetween(Carbon $start, Carbon $end)
    {
        $qb = parent::createQueryBuilder('temporaryAccessBooking');

        $expr = $qb->expr();
        $qb->addCriteria($this->between($start, $end));

        $q = $qb->getQuery();

        return $q->getResult();
    }

    /**
     * @param Carbon $start
     * @param Carbon $end
     * @param Building $building
     *
     * @return TemporaryAccessBooking[]
     */
    public function findBetweenForBuilding(Carbon $start, Carbon $end, Building $building)
    {
        $qb = parent::createQueryBuilder('temporaryAccessBooking');

        $expr = $qb->expr();
        $qb->innerJoin('temporaryAccessBooking.bookableArea', 'bookableArea')
            ->addCriteria($this->between($start, $end))
            ->andWhere($expr->eq('bookableArea.building', ':building'));

        $q = $qb->setParameter('building', $building)
            ->getQuery();

        return $q->getResult();
    }

    /**
     * @param Carbon $start
     * @param Carbon $end
     * @param Building $building
     * @param User $user
     *
     * @return TemporaryAccessBooking[]
     */
    public function findBetweenForBuildingAndUser(Carbon $start, Carbon $end, Building $building, User $user)
    {
        $qb = parent::createQueryBuilder('temporaryAccessBooking');
        $expr = $qb->expr();

        $qb->innerJoin('temporaryAccessBooking.bookableArea', 'bookableArea')
            ->addCriteria($this->between($start, $end))
            ->addCriteria($this->byUser($user))
            ->andWhere($expr->eq('bookableArea.building', ':building'));

        $q = $qb->setParameter('building', $building)
            ->getQuery();

        return $q->getResult();
    }

    /**
     * @param Carbon $start
     * @param Carbon $end
     * @param BookableArea $bookableArea
     *
     * @return TemporaryAccessBooking[]
     */
    public function findBetweenForBookableArea(Carbon $start, Carbon $end, BookableArea $bookableArea)
    {
        $qb = parent::createQueryBuilder('temporaryAccessBooking');
        $expr = $qb->expr();

        $qb->innerJoin('temporaryAccessBooking.bookableArea', 'bookableArea')
            ->addCriteria($this->between($start, $end))
            ->andWhere($expr->eq('bookableArea', ':bookableArea'));

        $q = $qb->setParameter('bookableArea', $bookableArea)
            ->getQuery();

        return $q->getResult();
    }

    /**
     * Save TemporaryAccessBooking to the DB.
     *
     * @param TemporaryAccessBooking $temporaryAccessBooking
     */
    public function save(TemporaryAccessBooking $temporaryAccessBooking)
    {
        $this->_em->persist($temporaryAccessBooking);
        $this->_em->flush();
    }

    /**
     * Remove a TemporaryAccessBooking.
     *
     * @param TemporaryAccessBooking $temporaryAccessBooking
     */
    public function remove(TemporaryAccessBooking $temporaryAccessBooking)
    {
        $this->_em->remove($temporaryAccessBooking);
        $this->_em->flush();
    }

    /**
     * Criteria to filter between to dates.
     *
     * @param Carbon $start
     * @param Carbon $end
     *
     * @return Criteria
     */
    protected function between(Carbon $start, Carbon $end)
    {
        $expr = Criteria::expr();
        $criteria = Criteria::create()
            ->where(
                $expr->andX(
                    $expr->lt('start', $end),
                    $expr->gt('end', $start)
                )
            )
            ->orderBy(['start' => Criteria::ASC]);

        return $criteria;
    }

    /**
     * Criteria to filter by User.
     *
     * @param User $user
     *
     * @return Criteria
     */
    protected function byUser(User $user)
    {
        $expr = Criteria::expr();

        return Criteria::create()
            ->where($expr->eq('user', $user));
    }
}
