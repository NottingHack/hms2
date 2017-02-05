<?php

namespace HMS\Repositories\Doctrine;

use Hms\Entities\Role;
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
     * @return Role|object
     */
    public function findByName(string $roleName)
    {
        return parent::findOneBy(['name' => $roleName]);
    }

    /**
     * store a new user in the DB.
     * @param  Role $role
     */
    public function save($role)
    {
        $this->_em->persist($role);
        $this->_em->flush();
    }
}
