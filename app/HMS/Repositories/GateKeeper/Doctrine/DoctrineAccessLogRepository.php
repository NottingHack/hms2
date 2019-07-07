<?php

namespace HMS\Repositories\GateKeeper\Doctrine;

use HMS\Entities\User;
use Doctrine\ORM\EntityRepository;
use HMS\Entities\GateKeeper\AccessLog;
use HMS\Repositories\GateKeeper\AccessLogRepository;

class DoctrineAccessLogRepository extends EntityRepository implements AccessLogRepository
{
    /**
     * @param User $user
     *
     * @return AccessLog[]
     */
    public function findByUser(User $user)
    {
        return parent::findByUser($user);
    }

    /**
     * @param User $user
     *
     * @return null|AccessLog
     */
    public function findLatestByUser(User $user)
    {
        return parent::findOneByUser($user, ['accessTime' => 'DESC']);
    }

    /**
     * Save AccessLog to the DB.
     *
     * @param AccessLog $accessLog
     */
    public function save(AccessLog $accessLog)
    {
        $this->_em->persist($accessLog);
        $this->_em->flush();
    }
}
