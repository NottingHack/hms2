<?php

namespace HMS\Repositories\Doctrine;

use Carbon\Carbon;
use Hms\Entities\Invite;
use Doctrine\ORM\EntityRepository;
use HMS\Repositories\InviteRepository;
use Doctrine\Common\Collections\Criteria;
use LaravelDoctrine\ORM\Pagination\PaginatesFromRequest;

class DoctrineInviteRepository extends EntityRepository implements InviteRepository
{
    use PaginatesFromRequest;

    /**
     * Create a new invite if not found.
     *
     * @param string $email
     *
     * @return Invite
     */
    public function findOrCreateByEmail($email)
    {
        $invite = $this->findOneByEmail($email);
        if (! $invite) {
            // Don't have a previous invite so create one
            $invite = new Invite();
            $invite->create($email);
            $this->_em->persist($invite);
            $this->_em->flush();
        }

        return $invite;
    }

    /**
     * Find an invite by emial.
     *
     * @param string $email
     *
     * @return null|Invite
     */
    public function findOneByEmail($email)
    {
        return parent::findOneByEmail($email);
    }

    /**
     * @param Carbon $start
     * @param Carbon $end
     *
     * @return Invite[]
     */
    public function findCreatedBetween(Carbon $start, Carbon $end)
    {
        $q = parent::createQueryBuilder('invite');

        $q = $q->where($q->expr()->between('invite.createdAt', ':start', ':end'))
            ->orWhere($q->expr()->between('invite.createdAt', ':start', ':end'));

        $q = $q->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery();

        return $q->getResult();
    }

    /**
     * Find an invite by partial email.
     *
     * @param string $searchQuery
     * @param bool $paginate
     * @param int $perPage
     * @param string $pageName
     *
     * @return Invite[]|array|\Illuminate\Pagination\LengthAwarePaginator
     */
    public function searchLike(
        string $searchQuery,
        bool $paginate = false,
        $perPage = 15,
        $pageName = 'page'
    ) {
        $q = parent::createQueryBuilder('invite')
            ->where('invite.email LIKE :keyword');

        $q = $q->setParameter('keyword', '%' . $searchQuery . '%')
            ->getQuery();

        if ($paginate) {
            return $this->paginate($q, $perPage, $pageName);
        }

        return $q->getResult();
    }

    /**
     * Find an invite by token.
     *
     * @param string $token
     *
     * @return null|Invite
     */
    public function findOneByInviteToken($token)
    {
        return parent::findOneByInviteToken($token);
    }

    /**
     * Remove all invites older than a given date.
     *
     * @param Carbon $date
     *
     * @return array
     */
    public function removeAllOlderThan(Carbon $date)
    {
        $criteria = Criteria::create();
        $criteria->where($criteria->expr()->lt('createdAt', $date));

        $invites = $this->matching($criteria)->toArray();

        foreach ($invites as $invite) {
            $this->_em->remove($invite);
        }
        $this->_em->flush();
    }

    /**
     * Remove a single invites.
     *
     * @param Invite $invite
     */
    public function remove(Invite $invite)
    {
        $this->_em->remove($invite);
        $this->_em->flush();
    }
}
