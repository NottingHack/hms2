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
     * @return Role|object
     */
    public function getMember()
    {
        return parent::findOneBy(['name' => 'member']);
    }
}