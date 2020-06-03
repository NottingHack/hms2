<?php

namespace HMS\Repositories\Gatekeeper\Doctrine;

use Carbon\Carbon;
use HMS\Entities\User;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\EntityRepository;
use HMS\Entities\Gatekeeper\Building;
use Doctrine\Common\Collections\Criteria;
use HMS\Entities\Gatekeeper\TemporaryAccessBooking;
use HMS\Repositories\Gatekeeper\TemporaryAccessBookingRepository;

class DoctrineTemporaryAccessBookingRepository extends EntityRepository implements TemporaryAccessBookingRepository
{
    /**
     * Check for any Bookings that would clash with a given start and end time.
     *
     * @param User $user
     * @param Carbon $start
     * @param Carbon $end
     *
     * @return TemporaryAccessBooking[]
     */
    public function checkForClashByUser(User $user, Carbon $start, Carbon $end)
    {
        $expr = Criteria::expr();
        $criteria = Criteria::create()
            ->where($expr->eq('user', $user))
            ->andWhere(
                $expr->orX(
                    $expr->andX(
                        $expr->lte('start', $start),
                        $expr->gt('end', $start)
                    ),
                    $expr->andX(
                        $expr->lt('start', $end),
                        $expr->gte('end', $end)
                    ),
                    $expr->andX(
                        $expr->gt('start', $start),
                        $expr->lt('start', $end)
                    )
                )
            )
            ->orderBy(['start' => Criteria::ASC]);

        return $this->matching($criteria)->toArray();
    }

    /**
     * Check for any Bookings that would clash with a given start and end time.
     *
     * @param User $user
     * @param Building $building
     * @param Carbon $start
     * @param Carbon $end
     *
     * @return TemporaryAccessBooking[]
     */
    public function checkForClashByUserForBuilding(User $user, Building $building, Carbon $start, Carbon $end)
    {
        $expr = Criteria::expr();
        $criteria = Criteria::create()
            ->where($expr->eq('user', $user))
            ->andWhere(
                $expr->orX(
                    $expr->andX(
                        $expr->lte('start', $start),
                        $expr->gt('end', $start)
                    ),
                    $expr->andX(
                        $expr->lt('start', $end),
                        $expr->gte('end', $end)
                    ),
                    $expr->andX(
                        $expr->gt('start', $start),
                        $expr->lt('start', $end)
                    )
                )
            )
            ->orderBy(['start' => Criteria::ASC]);

        $qb = parent::createQueryBuilder('temporaryAccessBooking');
        $qbExpr = $qb->expr();
        $qb->innerJoin('temporaryAccessBooking.bookableArea', 'bookableArea')
            ->addCriteria($criteria)
            ->andWhere($qbExpr->eq('bookableArea.building', ':building'));

        $q = $qb->setParameter('building', $building)
            ->getQuery();

        return $q->getResult();
    }

    /**
     * Get any current bookings.
     *
     * @return TemporaryAccessBooking[]
     */
    public function findCurrent()
    {
        $now = Carbon::now();

        $expr = Criteria::expr();
        $criteria = Criteria::create()
            ->where(
                $expr->andX(
                    $expr->lte('start', $now),
                    $expr->gte('end', $now)
                )
            )
            ->orderBy(['start' => Criteria::ASC]);

        return $this->matching($criteria)->toArray();
    }

    /**
     * Count future bookings for a User on a given Building.
     *
     * @param Building $building
     * @param User $user
     *
     * @return int
     */
    public function countFutureByBuildingAndUser(Building $building, User $user): int
    {
        $now = Carbon::now();
        // can not use Criteria cause of the join :(
        $qb = parent::createQueryBuilder('temporaryAccessBooking');
        $expr = $qb->expr();

        $qb->select('COUNT(temporaryAccessBooking.id)')
            ->innerJoin('temporaryAccessBooking.bookableArea', 'bookableArea')
            ->where(
                $expr->andX(
                    $expr->eq('bookableArea.building', ':building'),
                    $expr->eq('temporaryAccessBooking.user', ':user'),
                    $expr->gte('temporaryAccessBooking.end', ':now')
                )
            );

        $qb->setParameter('building', $building)
            ->setParameter('user', $user)
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

        $subQuery = $qb->select('COUNT(temporaryAccessBooking.id)')
            ->innerJoin('temporaryAccessBooking.bookableArea', 'bookableArea')
            ->where('bookableArea.building = b.id')
            ->andWhere('temporaryAccessBooking.user = :user')
            ->andWhere('temporaryAccessBooking.end >= :now')
            ->getDQL();

        $qb1 = $this->_em->createQueryBuilder();
        $qb1->select('b AS building')
            ->addSelect('(' . $subQuery . ') AS booking_count')
            ->from(Building::class, 'b');

        $qb1->setParameter('user', $user)
            ->setParameter('now', $now);

        $results = $qb1->getQuery()->getResult();

        $results = collect($results)->mapWithKeys(function ($item) {
            return [($item['building']->getId()) => (int) $item['booking_count']];
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
            $qb1 = parent::createQueryBuilder('tab');

            $qb1->leftJoin('tab.bookableArea', 'ba')
                ->leftJoin('ba.building', 'b')
                ->where('tab.user = :user')
                ->andWhere('b.id = :building_id')
                ->andWhere('tab.end = :end')
                ->setMaxResults(1);

            $qb1->setParameter('user', $user)
                ->setParameter('building_id', $end['building_id'])
                ->setParameter('end', $end['end']);

            $booking = $qb1->getQuery()->getSingleResult();
            $results[$end['building_id']] = $booking;
        }

        return $results;
    }

    /**
     * @param Carbon $start
     * @param Carbon $end
     *
     * @return TemporaryAccessBooking[]
     */
    public function findBetween(Carbon $start, Carbon $end)
    {
        $q = parent::createQueryBuilder('temporaryAccessBooking');

        $expr = $q->expr();
        $q = $q->where($expr->between('temporaryAccessBooking.start', ':start', ':end'))
            ->orWhere($expr->between('temporaryAccessBooking.end', ':start', ':end'))
            ->orWhere(
                $expr->andX(
                    $expr->lte('temporaryAccessBooking.start', ':start'),
                    $expr->gte('temporaryAccessBooking.end', ':end')
                )
            )
            ->orderBy('temporaryAccessBooking.start', 'ASC');

        $q = $q->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery();

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
        $q = parent::createQueryBuilder('temporaryAccessBooking');

        $expr = $q->expr();
        $q = $q->innerJoin('temporaryAccessBooking.bookableArea', 'bookableArea')
            ->where($expr->between('temporaryAccessBooking.start', ':start', ':end'))
            ->orWhere($expr->between('temporaryAccessBooking.end', ':start', ':end'))
            ->orWhere(
                $expr->andX(
                    $expr->lte('temporaryAccessBooking.start', ':start'),
                    $expr->gte('temporaryAccessBooking.end', ':end')
                )
            )
            ->andWhere($expr->eq('bookableArea.building', ':building'))
            ->orderBy('temporaryAccessBooking.start', 'ASC');

        $q = $q->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('building', $building)
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
}
