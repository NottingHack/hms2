<?php

namespace HMS\Repositories\Tools\Doctrine;

use Carbon\Carbon;
use HMS\Entities\User;
use HMS\Entities\Tools\Tool;
use HMS\Entities\Tools\Booking;
use Doctrine\ORM\EntityRepository;
use HMS\Entities\Tools\BookingType;
use Doctrine\Common\Collections\Criteria;
use HMS\Repositories\Tools\BookingRepository;

class DoctrineBookingRepository extends EntityRepository implements BookingRepository
{
    /**
     * Get the current booking for a tool.
     * @param  Tool   $tool
     * @return null|Booking
     */
    public function currnetForTool(Tool $tool)
    {
        $now = Carbon::now();

        $expr = Criteria::expr();
        $criteria = Criteria::create()
            ->where(
                $expr->andX(
                    $expr->lte('start', $now),
                    $expr->gte('end', $now),
                    $expr->eq('tool', $tool)
                )
            )
            ->orderBy(['start' => Criteria::ASC])
            ->setMaxResults(1);

        $results = $this->matching($criteria);

        return $results->isEmpty() ? null : $results->first();
    }

    /**
     * Get the next booking for a tool.
     * @param  Tool   $tool
     * @return null|Booking
     */
    public function nextForTool(Tool $tool)
    {
        $now = Carbon::now();

        $expr = Criteria::expr();
        $criteria = Criteria::create()
            ->where(
                $expr->andX(
                    $expr->gte('start', $now),
                    $expr->eq('tool', $tool)
                )
            )
            ->orderBy(['start' => Criteria::ASC])
            ->setMaxResults(1);

        $results = $this->matching($criteria);

        return $results->isEmpty() ? null : $results->first();
    }

    /**
     * Check for any Bookings that would clash with a given start and end time.
     *
     * @param  Tool   $tool
     * @param  Carbon $start
     * @param  Carbon $end
     * @return Bookings[]
     */
    public function checkForClashByTool(Tool $tool, Carbon $start, Carbon $end)
    {
        $expr = Criteria::expr();
        $criteria = Criteria::create()
            ->where($expr->eq('tool', $tool))
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
     * @param Tool $tool
     * @return Booking[]
     */
    public function findByTool(Tool $tool)
    {
        return parent::findByTool($tool, ['start' => 'ASC']);
    }

    /**
     * @param  Tool   $tool
     * @param  User   $user
     * @return Booking[]
     */
    public function findByToolAndUser(Tool $tool, User $user)
    {
        return parent::findBy(['tool' => $tool, 'user' => $user], ['start' => 'ASC']);
    }

    /**
     * Count normal booing for a User on a given Tool.
     *
     * @param  Tool   $tool
     * @param  User   $user
     * @return int
     */
    public function countNormalByToolAndUser(Tool $tool, User $user)
    {
        return count(parent::findBy(['tool' => $tool, 'user' => $user, 'type' => BookingType::NORMAL], ['start' => 'ASC']));
    }

    /**
     * @param Tool $tool
     * @param Carbon $start
     * @param Carbon $end
     * @return Booking[]
     */
    public function findByToolBetween(Tool $tool, Carbon $start, Carbon $end)
    {
        $q = parent::createQueryBuilder('booking');

        $q = $q->where($q->expr()->between('booking.start', ':start', ':end'))
            ->orWhere($q->expr()->between('booking.end', ':start', ':end'))
            ->andWhere('booking.tool = :tool_id')
            ->orderBy('booking.start', 'ASC');

        $q = $q->setParameter('tool_id', $tool->getId())
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery();

        return $q->getResult();
    }

    /**
     * @param Tool $tool
     * @return Booking[]
     */
    public function findNormalByTool(Tool $tool)
    {
        return parent::findBy(['tool' => $tool, 'type' => BookingType::NORMAL], ['start' => 'ASC']);
    }

    /**
     * @param Tool $tool
     * @return Booking[]
     */
    public function findInductionByTool(Tool $tool)
    {
        return parent::findBy(['tool' => $tool, 'type' => BookingType::INDCUTION], ['start' => 'ASC']);
    }

    /**
     * @param Tool $tool
     * @return Booking[]
     */
    public function findMaintenanceByTool(Tool $tool)
    {
        return parent::findBy(['tool' => $tool, 'type' => BookingType::MAINTENANCE], ['start' => 'ASC']);
    }

    /**
     * save Booking to the DB.
     * @param  Booking $booking
     */
    public function save(Booking $booking)
    {
        $this->_em->persist($booking);
        $this->_em->flush();
    }

    /**
     * Remove a Booking.
     * @param  Booking $booking
     */
    public function remove(Booking $booking)
    {
        $this->_em->remove($booking);
        $this->_em->flush();
    }
}