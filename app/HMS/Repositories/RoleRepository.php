<?php

namespace HMS\Repositories;

use Hms\Entities\Role;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;

class RoleRepository extends EntityRepository
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
     * Finds a role based on the role name
     *
     * @param  string $roleName name of the role we want
     * @return Role|object
     */
    public function findByName(string $roleName)
    {
        return parent::findOneBy(['name' => $roleName]);
    }
}
