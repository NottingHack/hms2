<?php

namespace HMS\Repositories\Governance\Doctrine;

use HMS\Entities\User;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\EntityRepository;
use HMS\Entities\Governance\Proxy;
use HMS\Entities\Governance\Meeting;
use HMS\Repositories\Governance\ProxyRepository;

class DoctrineProxyRepository extends EntityRepository implements ProxyRepository
{
    /**
     * Find all Proxies for a meeting by Proxy (User).
     *
     * @param Meeting $meeting
     * @param User $proxy
     *
     * @return Proxy[]
     */
    public function findByProxy(Meeting $meeting, User $proxy)
    {
        return parent::findBy(['meeting' => $meeting, 'proxy' => $proxy], ['proxy' => 'ASC']);
    }

    /**
     * Find Proxy for a meeting by Principal (User).
     *
     * @param Meeting $meeting
     * @param User $principal
     *
     * @return Proxy|null
     */
    public function findOneByPrincipal(Meeting $meeting, User $principal)
    {
        return parent::findOneBy(['meeting' => $meeting, 'principal' => $principal]);
    }

    /**
     * For a given meeting count the proxied represented by the Checked-in Attendees.
     *
     * @param Meeting $meeting
     *
     * @return int
     */
    public function countRepresentedForMeeting(Meeting $meeting)
    {
        $q = parent::createQueryBuilder('proxies')
            ->select('COUNT(proxies.id)')
            ->innerJoin('proxies.meeting', 'meeting')
            ->innerJoin('meeting.attendees', 'attendees', Join::WITH, 'attendees.id = proxies.proxy')
            ->where('meeting.id = :meeting_id');

        $q->setParameter('meeting_id', $meeting->getId());

        return (int) $q->getQuery()->getSingleScalarResult();
    }

    /**
     * Save Proxy to the DB.
     *
     * @param Proxy $proxy
     */
    public function save(Proxy $proxy)
    {
        $this->_em->persist($proxy);
        $this->_em->flush();
    }

    /**
     * Remove a single Proxy.
     *
     * @param Proxy $proxy
     */
    public function remove(Proxy $proxy)
    {
        $this->_em->remove($proxy);
        $this->_em->flush();
    }
}
