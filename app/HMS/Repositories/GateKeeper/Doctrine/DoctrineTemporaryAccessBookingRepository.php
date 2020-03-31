<?php

namespace HMS\Repositories\GateKeeper\Doctrine;

use Carbon\Carbon;
use HMS\Entities\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\Criteria;
use HMS\Entities\GateKeeper\TemporaryAccessBooking;
use HMS\Repositories\GateKeeper\TemporaryAccessBookingRepository;

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
     * @param Carbon $start
     * @param Carbon $end
     *
     * @return TemporaryAccessBooking[]
     */
    public function findBetween(Carbon $start, Carbon $end)
    {
        $q = parent::createQueryBuilder('booking');

        $expr = $q->expr();
        $q = $q->where($expr->between('booking.start', ':start', ':end'))
            ->orWhere($expr->between('booking.end', ':start', ':end'))
            ->orWhere(
                $expr->andX(
                    $expr->lte('booking.start', ':start'),
                    $expr->gte('booking.end', ':end')
                )
            )
            ->orderBy('booking.start', 'ASC');

        $q = $q->setParameter('start', $start)
            ->setParameter('end', $end)
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
