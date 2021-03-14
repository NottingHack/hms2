<?php

namespace HMS\Repositories\Membership\Doctrine;

use Doctrine\ORM\EntityRepository;
use HMS\Entities\Membership\RejectedLog;
use HMS\Entities\User;
use HMS\Repositories\Membership\RejectedLogRepository;

class DoctrineRejectedLogRepository extends EntityRepository implements RejectedLogRepository
{
    /**
     * @param User $user
     *
     * @return RejectedLog[]
     */
    public function findByUser(User $user)
    {
        return parent::findByUser($user);
    }

    /**
     * Save RejectedLog to the DB.
     *
     * @param RejectedLog $rejectedLog
     */
    public function save(RejectedLog $rejectedLog)
    {
        $this->_em->persist($rejectedLog);
        $this->_em->flush();
    }
}
