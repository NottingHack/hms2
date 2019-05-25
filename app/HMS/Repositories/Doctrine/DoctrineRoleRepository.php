<?php

namespace HMS\Repositories\Doctrine;

use HMS\Entities\Role;
use HMS\Entities\User;
use Doctrine\ORM\EntityRepository;
use HMS\Repositories\RoleRepository;
use Doctrine\Common\Collections\ArrayCollection;

class DoctrineRoleRepository extends EntityRepository implements RoleRepository
{
    /**
     * @param $id
     *
     * @return Role|null
     */
    public function findOneById($id)
    {
        return parent::findOneById($id);
    }

    /**
     * Finds all entities in the repository.
     *
     * @return ArrayCollection The entities.
     */
    public function findAll()
    {
        $entities = parent::findAll();
        $roles = [];
        foreach ($entities as $entity) {
            $roles[$entity->getName()] = $entity;
        }

        return new ArrayCollection($roles);
    }

    /**
     * Finds a role based on the role name.
     *
     * @param string $roleName name of the role we want
     *
     * @return Role|nul
     */
    public function findOneByName(string $roleName)
    {
        return parent::findOneByName($roleName);
    }

    /**
     * @param string $email
     *
     * @return Role|null
     */
    public function findOneByEmail(string $email)
    {
        return parent::findOneByEmail($email);
    }

    /**
     * Find all the team roles a given user has.
     *
     * @param User $user
     *
     * @return Roles[]
     */
    public function findTeamsForUser(User $user)
    {
        $q = parent::createQueryBuilder('role')
            ->leftJoin('role.users', 'user')
            ->where('role.name LIKE :name')
            ->andWhere('user.id = :user_id');

        $q = $q->setParameter('name', 'team.%')
            ->setParameter('user_id', $user->getId())
            ->getQuery();

        return $q->getResult();
    }

    /**
     * Store a new user in the DB.
     *
     * @param Role $role
     */
    public function save(Role $role)
    {
        $this->_em->persist($role);
        $this->_em->flush();
    }

    /**
     * Remove a role based on the role name.
     *
     * @param string $roleName
     */
    public function removeOneByName(string $roleName)
    {
        $role = $this->findOneByName($roleName);
        if (! is_null($role)) {
            $this->_em->remove($role);
            $this->_em->flush();
        }
    }
}
