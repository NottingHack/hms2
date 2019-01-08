<?php

namespace HMS\Repositories\Doctrine;

use HMS\Entities\Role;
use Doctrine\ORM\EntityRepository;
use HMS\Repositories\RoleRepository;
use Doctrine\Common\Collections\ArrayCollection;

class DoctrineRoleRepository extends EntityRepository implements RoleRepository
{
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
     * @param  string $roleName name of the role we want
     * @return Role|nul
     */
    public function findOneByName(string $roleName)
    {
        return parent::findOneByName($roleName);
    }

    /**
     * @param  string $email
     * @return Role|null
     */
    public function findOneByEmail(string $email)
    {
        return parent::findOneByEmail($email);
    }

    /**
     * store a new user in the DB.
     * @param  Role $role
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
        if ( ! is_null($role)) {
            $this->_em->remove($role);
            $this->_em->flush();
        }
    }
}
