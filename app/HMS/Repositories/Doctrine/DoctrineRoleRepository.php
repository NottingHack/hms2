<?php

namespace HMS\Repositories\Doctrine;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use HMS\Entities\Role;
use HMS\Entities\User;
use HMS\Repositories\RoleRepository;

class DoctrineRoleRepository extends EntityRepository implements RoleRepository
{
    /**
     * @param int $id
     *
     * @return Role|null
     */
    public function findOneById(int $id)
    {
        return parent::findOneById($id);
    }

    /**
     * Finds all entities in the repository.
     *
     * @return Role[]
     */
    public function findAll()
    {
        return parent::findAll();
    }

    /**
     * Finds all entities in the repository.
     *
     * @return Role[]
     */
    public function findAllTeams()
    {
        $q = parent::createQueryBuilder('role')
            ->where('role.name LIKE :name')
            ->orderBy('role.displayName', 'ASC');

        $q = $q->setParameter('name', 'team.%')
            ->getQuery();

        return $q->getResult();
    }

    /**
     * Finds a role based on the role name.
     *
     * @param string $roleName name of the role we want
     *
     * @return Role|null
     */
    public function findOneByName(string $roleName)
    {
        return parent::findOneByName($roleName);
    }

    /**
     * Finds a role based on the role name.
     *
     * @param string $roleDisplayName name of the role we want
     *
     * @return Role|null
     */
    public function findOneByDisplayName(string $roleDisplayName)
    {
        return parent::findOneByDisplayName($roleDisplayName);
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
     * Find the member role of a given user.
     *
     * @param User $user
     *
     * @return Role|null
     */
    public function findMemberStatusForUser(User $user)
    {
        $q = parent::createQueryBuilder('role')
            ->leftJoin('role.users', 'user')
            ->where('role.name LIKE :name')
            ->andWhere('user.id = :user_id')
            ->setMaxResults(1);

        $q = $q->setParameter('name', 'member.%')
            ->setParameter('user_id', $user->getId())
            ->getQuery();

        return $q->getOneOrNullResult();
    }

    /**
     * Find all the team roles a given user has.
     *
     * @param User $user
     *
     * @return Role[]
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
