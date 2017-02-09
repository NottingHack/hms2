<?php

namespace HMS\Repositories\Doctrine;

use Carbon\Carbon;
use Hms\Entities\Invite;
use Doctrine\ORM\EntityRepository;
use HMS\Repositories\InviteRepository;
use Doctrine\Common\Collections\Criteria;

class DoctrineInviteRepository extends EntityRepository implements InviteRepository
{
    /**
     * Create a new invite if not found.
     *
     * @param  string $email
     * @return Invite
     */
    public function findOrCreateByEmail($email)
    {
        $invite = $this->findOneByEmail($email);
        if ( ! $invite) {
            // Don't have a previous invite so create one
            $invite = new Invite();
            $invite->create($email);
            $this->_em->persist($invite);
            $this->_em->flush();
        }

        return $invite;
    }

    /**
     * find an invite by emial.
     *
     * @param  string $email
     * @return Invite
     */
    public function findOneByEmail($email)
    {
        return parent::findOneByEmail($email);
    }

    /**
     * find an invite by token.
     *
     * @param  string $token
     * @return Invite
     */
    public function findOneByInviteToken($token)
    {
        return parent::findOneByInviteToken($token);
    }

    /**
     * remove all invites older than a given date.
     * @param  Carbon $date
     * @return array
     */
    public function removeAllOlderThan(Carbon $date)
    {
        $criteria = new Criteria();
        $criteria->where($criteria->expr()->lt('createdAt', $date));

        $invites = $this->matching($criteria)->toArray();

        foreach ($invites as $invite) {
            $this->_em->remove($invite);
        }
        $this->_em->flush();
    }

    /**
     * remove a single invites.
     * @param  Invite $invite
     */
    public function remove(Invite $invite)
    {
        $this->_em->remove($invite);
        $this->_em->flush();
    }
}
