<?php

namespace HMS\Repositories\Doctrine;

use HMS\Entities\User;
use Doctrine\ORM\EntityRepository;
use HMS\Repositories\UserRepository;
use LaravelDoctrine\ORM\Pagination\Paginatable;

class DoctrineUserRepository extends EntityRepository implements UserRepository
{
    use Paginatable;

    /**
     * @param  $id
     * @return array
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
     * @param  string $searchQuery
     * @return array
     */
    public function searchLike(string $searchQuery)
    {
        $q = parent::createQueryBuilder('user')
            ->leftJoin('user.profile', 'profile')
            ->leftJoin('user.account', 'account')
            ->where('user.firstname LIKE :keyword')
            ->orWhere('user.lastname LIKE :keyword')
            ->orWhere('user.username LIKE :keyword')
            ->orWhere('user.email LIKE :keyword')
            ->orWhere('profile.addressPostcode LIKE :keyword')
            ->orWhere('account.paymentRef LIKE :keyword')
            ->setParameter('keyword', '%'.$searchQuery.'%')
            ->getQuery();
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
}
