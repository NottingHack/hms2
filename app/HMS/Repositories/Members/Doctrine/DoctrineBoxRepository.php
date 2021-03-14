<?php

namespace HMS\Repositories\Members\Doctrine;

use Doctrine\ORM\EntityRepository;
use HMS\Entities\Members\Box;
use HMS\Entities\Members\BoxState;
use HMS\Entities\Role;
use HMS\Entities\User;
use HMS\Repositories\Members\BoxRepository;
use LaravelDoctrine\ORM\Pagination\PaginatesFromRequest;

class DoctrineBoxRepository extends EntityRepository implements BoxRepository
{
    use PaginatesFromRequest;

    /**
     * Count all boxes.
     *
     * @return int Total number of boxes INUSE
     */
    public function count(array $criteria = [])
    {
        return parent::count($criteria);
    }

    /**
     * Count all boxes INUSE.
     *
     * @return int Total number of boxes INUSE
     */
    public function countAllInUse()
    {
        return parent::countByState(BoxState::INUSE);
    }

    /**
     * Count all boxes REMOVED.
     *
     * @return int Total number of boxes REMOVED
     */
    public function countAllRemoved()
    {
        return parent::countByState(BoxState::REMOVED);
    }

    /**
     * Count all boxes ABANDONED.
     *
     * @return int Total number of boxes ABANDONED
     */
    public function countAllAbandoned()
    {
        return parent::countByState(BoxState::ABANDONED);
    }

    /**
     * Count how many boxes a User has INUSE.
     *
     * @param User $user
     *
     * @return int Number of boxes this user has INUSE
     */
    public function countInUseByUser(User $user)
    {
        return parent::count(['user' => $user, 'state' => BoxState::INUSE]);
    }

    /**
     * Paginate any boxes that are marked INUSE but owned by an Ex member.
     *
     * @param int $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateInUseByExMember($perPage = 15, $pageName = 'page')
    {
        $q = parent::createQueryBuilder('box')
            ->leftJoin('box.user', 'user')->addSelect('user')
            ->leftJoin('user.roles', 'role')
            ->where('box.state = :state')
            ->andWhere('role.name = :role_name');

        $q = $q->setParameter('state', BoxState::INUSE)
            ->setParameter('role_name', Role::MEMBER_EX)
            ->getQuery();

        return $this->paginate($q, $perPage, $pageName);
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
        $q = parent::createQueryBuilder('box')
            ->where('box.user = :user_id');

        $q = $q->setParameter('user_id', $user->getId())->getQuery();

        return $this->paginate($q, $perPage, $pageName);
    }

    /**
     * Find by user.
     *
     * @param User $user
     *
     * @return Box[]
     */
    public function findByUser(User $user)
    {
        return parent::findByUser($user);
    }

    /**
     * Save Box to the DB.
     *
     * @param Box $box
     */
    public function save(Box $box)
    {
        $this->_em->persist($box);
        $this->_em->flush();
    }
}
