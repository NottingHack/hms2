<?php

namespace HMS\Repositories;

use Doctrine\ORM\EntityRepository;

class DoctrineUserRepository extends EntityRepository implements UserRepository
{
    /**
     * @param  int $id
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
    public function findByUsername($username)
    {
        return parent::findByUsername($username);
    }

    /**
     * @param  string $email
     * @return array
     */
    public function findByEmail($email)
    {
        return parent::findByEmail($email);
    }

    /**
     * store a new user in the DB
     * @param  User $user
     */
    public function create($user)
    {
        $this->_em->persist($user);
        $this->_em->flush();
    }
}
