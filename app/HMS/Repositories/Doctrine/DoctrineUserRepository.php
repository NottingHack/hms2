<?php

namespace HMS\Repositories\Doctrine;

use HMS\Entities\Role;
use HMS\Entities\User;
use Doctrine\ORM\EntityRepository;
use HMS\Repositories\UserRepository;
use LaravelDoctrine\ORM\Pagination\PaginatesFromRequest;

class DoctrineUserRepository extends EntityRepository implements UserRepository
{
    use PaginatesFromRequest;

    /**
     * @param  $id
     * @return null|User
     */
    public function find($id)
    {
        return parent::find($id);
    }

    /**
     * @param  string $username
     * @return array
     */
    public function findByUsername(string $username)
    {
        return parent::findByUsername($username);
    }

    /**
     * @param  string $email
     * @return array
     */
    public function findByEmail(string $email)
    {
        return parent::findByEmail($email);
    }

    /**
     * @param  string $email
     * @return User|null
     */
    public function findOneByEmail(string $email)
    {
        return parent::findOneByEmail($email);
    }

    /**
     * @param  string $searchQuery
     * @param  bool $hasAccount limit to users with associated accounts
     * @param bool $paginate
     * @param int    $perPage
     * @param string $pageName
     * @return User[]|array|\Illuminate\Pagination\LengthAwarePaginator
     */
    public function searchLike(string $searchQuery,
        ?bool $hasAccount = false,
        bool $paginate = false,
        $perPage = 15,
        $pageName = 'page')
    {
        $q = parent::createQueryBuilder('user')
            ->leftJoin('user.profile', 'profile')->addSelect('profile')
            ->leftJoin('user.account', 'account')->addSelect('account')
            ->where('user.name LIKE :keyword')
            ->orWhere('user.lastname LIKE :keyword')
            ->orWhere('user.username LIKE :keyword')
            ->orWhere('user.email LIKE :keyword')
            ->orWhere('profile.addressPostcode LIKE :keyword')
            ->orWhere('account.paymentRef LIKE :keyword');

        if ($hasAccount) {
            $q = $q->andWhere('user.account IS NOT NULL');
        }

        $q = $q->setParameter('keyword', '%'.$searchQuery.'%')
            ->getQuery();

        if ($paginate) {
            return $this->paginate($q, $perPage, $pageName);
        }

        return $q->getResult();
    }

    /**
     * save User to the DB.
     * @param  User $user
     */
    public function save(User $user)
    {
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * @param Role $role
     * @param int    $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateUsersWithRole(Role $role, $perPage = 15, $pageName = 'page')
    {
        $q = parent::createQueryBuilder('user')
            ->leftJoin('user.roles', 'role')->addSelect('role')
            ->where('role.id = :role_id');

        $q = $q->setParameter('role_id', $role->getId())->getQuery();

        return $this->paginate($q, $perPage, $pageName);
    }
}
