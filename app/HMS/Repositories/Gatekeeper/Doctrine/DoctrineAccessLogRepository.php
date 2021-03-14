<?php

namespace HMS\Repositories\Gatekeeper\Doctrine;

use Carbon\Carbon;
use Doctrine\ORM\EntityRepository;
use HMS\Entities\Gatekeeper\AccessLog;
use HMS\Entities\User;
use HMS\Repositories\Gatekeeper\AccessLogRepository;
use LaravelDoctrine\ORM\Pagination\PaginatesFromRequest;

class DoctrineAccessLogRepository extends EntityRepository implements AccessLogRepository
{
    use PaginatesFromRequest;

    /**
     * Finds all entities in the repository.
     *
     * @return array The entities.
     */
    public function findAll()
    {
        return parent::findAll();
    }

    /**
     * @return AccessLog|null
     */
    public function findFirst()
    {
        return parent::findOneBy([]);
    }

    /**
     * @param User $user
     *
     * @return AccessLog[]
     */
    public function findByUser(User $user)
    {
        return parent::findByUser($user, ['accessTime' => 'DESC']);
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
     * @param User $user
     * @param int $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateByUser(User $user, $perPage = 15, $pageName = 'page')
    {
        $q = parent::createQueryBuilder('accessLog')
            ->where('accessLog.user = :user_id')
            ->orderBy('accessLog.accessTime', 'DESC');

        $q = $q->setParameter('user_id', $user->getId())->getQuery();

        return $this->paginate($q, $perPage, $pageName);
    }

    /**
     * Finds all entities in the repository between dates.
     *
     * @param Carbon $start
     * @param Carbon $end
     * @param int $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateBetweeenAccessTimes(Carbon $start, Carbon $end, $perPage = 15, $pageName = 'page')
    {
        $q = parent::createQueryBuilder('accessLog');

        $q = $q->where($q->expr()->between('accessLog.accessTime', ':start', ':end'));

        $q = $q->setParameter('start', $start->copy())
            ->setParameter('end', $end->copy())
            ->getQuery();

        return $this->paginate($q, $perPage, $pageName);
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
