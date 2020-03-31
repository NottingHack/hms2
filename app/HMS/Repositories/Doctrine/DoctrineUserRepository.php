<?php

namespace HMS\Repositories\Doctrine;

use Carbon\Carbon;
use HMS\Entities\Role;
use HMS\Entities\User;
use Doctrine\ORM\EntityRepository;
use HMS\Governance\VotingPreference;
use HMS\Repositories\UserRepository;
use LaravelDoctrine\ORM\Pagination\PaginatesFromRequest;

class DoctrineUserRepository extends EntityRepository implements UserRepository
{
    use PaginatesFromRequest;

    /**
     * @param $id
     *
     * @return null|User
     */
    public function findOneById($id)
    {
        return parent::findOneById($id);
    }

    /**
     * @param string $username
     *
     * @return null|User
     */
    public function findOneByUsername(string $username)
    {
        return parent::findOneByUsername($username);
    }

    /**
     * @param string $email
     *
     * @return array
     */
    public function findByEmail(string $email)
    {
        return parent::findByEmail($email);
    }

    /**
     * @param string $email
     *
     * @return User|null
     */
    public function findOneByEmail(string $email)
    {
        return parent::findOneByEmail($email);
    }

    /**
     * Find Voting members base on Physical access in last six months.
     *
     * @return User[]
     */
    public function findVotingPhysical()
    {
        $q = parent::createQueryBuilder('user');

        $q->leftjoin('user.pin', 'pin')->addSelect('pin')
            ->leftjoin('user.profile', 'profile')->addSelect('profile')
            ->leftJoin('user.rfidTags', 'rfidTags')
            ->innerJoin('user.roles', 'role')
            ->where('role.name = :role_name')
            ->andWhere('rfidTags.lastUsed >= :sixMonthsAgo');

        $q->setParameter('role_name', Role::MEMBER_CURRENT)
            ->setParameter('sixMonthsAgo', Carbon::now()->subMonthsNoOverflow(6));

        $q = $q->getQuery();

        return $q->getResult();
    }

    /**
     * Find stated Voting members in last six months.
     *
     * @return User[]
     */
    public function findVotingStated()
    {
        $sixMonthsAgo = Carbon::now()->subMonthsNoOverflow(6);

        $q = parent::createQueryBuilder('user');

        $q->leftjoin('user.pin', 'pin')->addSelect('pin')
            ->leftjoin('user.profile', 'profile')->addSelect('profile')
            ->innerJoin('user.roles', 'role')
            ->where('role.name = :role_name')
            ->andWhere('profile.votingPreference = :votingPreference')
            ->andWhere('profile.votingPreferenceStatedAt >= :sixMonthsAgo');

        $q->setParameter('role_name', Role::MEMBER_CURRENT)
            ->setParameter('votingPreference', VotingPreference::VOTING)
            ->setParameter('sixMonthsAgo', $sixMonthsAgo);

        $q = $q->getQuery();

        return $q->getResult();
    }

    /**
     * Find stated Non-voting members in last six months.
     *
     * @return User[]
     */
    public function findNonVotingStated()
    {
        $sixMonthsAgo = Carbon::now()->subMonthsNoOverflow(6);

        $q = parent::createQueryBuilder('user');

        $q->leftjoin('user.pin', 'pin')->addSelect('pin')
            ->leftjoin('user.profile', 'profile')->addSelect('profile')
            ->innerJoin('user.roles', 'role')
            ->where('role.name = :role_name')
            ->andWhere('profile.votingPreference = :votingPreference')
            ->andWhere('profile.votingPreferenceStatedAt >= :sixMonthsAgo');

        $q->setParameter('role_name', Role::MEMBER_CURRENT)
            ->setParameter('votingPreference', VotingPreference::NONVOTING)
            ->setParameter('sixMonthsAgo', $sixMonthsAgo);

        $q = $q->getQuery();

        return $q->getResult();
    }

    /**
     * Count Current Members.
     *
     * @return int
     */
    public function countCurrentMembers(): int
    {
        $qb = parent::createQueryBuilder('user');

        $qb->select('COUNT(user.id)')
            ->innerJoin('user.roles', 'roles')
            ->where('roles.name = :role_name');

        $qb->setParameter('role_name', Role::MEMBER_CURRENT);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param string $searchQuery
     * @param bool $hasAccount limit to users with associated accounts
     * @param bool $currentOnly limit to only MEMBER_CURRENT users
     * @param bool $paginate
     * @param int $perPage
     * @param string $pageName
     *
     * @return User[]|array|\Illuminate\Pagination\LengthAwarePaginator
     */
    public function searchLike(
        string $searchQuery,
        bool $hasAccount = false,
        bool $currentOnly = false,
        bool $paginate = false,
        $perPage = 15,
        $pageName = 'page'
    ) {
        $q = parent::createQueryBuilder('user')
            ->leftJoin('user.profile', 'profile')->addSelect('profile')
            ->leftJoin('user.account', 'account')->addSelect('account')
            ->innerJoin('user.roles', 'role')->addSelect('role')
            ->where('CONCAT(user.name, \' \', user.lastname) LIKE :keyword')
            ->orWhere('user.username LIKE :keyword')
            ->orWhere('user.email LIKE :keyword')
            ->orWhere('profile.addressPostcode LIKE :keyword')
            ->orWhere('account.paymentRef LIKE :keyword');

        if ($hasAccount) {
            $q = $q->andWhere('user.account IS NOT NULL');
        }

        if ($currentOnly) {
            $q = $q->andWhere('role.name = :role_name');
            $q = $q->setParameter('role_name', Role::MEMBER_CURRENT);
        }

        $q = $q->orderBy('CONCAT(user.name, \' \', user.lastname)', 'ASC');

        $q = $q->setParameter('keyword', '%' . $searchQuery . '%')
            ->getQuery();

        if ($paginate) {
            return $this->paginate($q, $perPage, $pageName);
        }

        return $q->getResult();
    }

    /**
     * Save User to the DB.
     *
     * @param User $user
     */
    public function save(User $user)
    {
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * @param Role $role
     * @param int $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateUsersWithRole(Role $role, $perPage = 15, $pageName = 'page')
    {
        $q = parent::createQueryBuilder('user')
            ->leftJoin('user.roles', 'role')
            ->where('role.id = :role_id');

        $q = $q->setParameter('role_id', $role->getId())->getQuery();

        return $this->paginate($q, $perPage, $pageName);
    }
}
