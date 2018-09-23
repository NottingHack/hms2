<?php

namespace HMS\Repositories\Doctrine;

use Doctrine\ORM\EntityRepository;
use HMS\Repositories\PermissionRepository;
use LaravelDoctrine\ACL\Permissions\Permission;

class DoctrinePermissionRepository extends EntityRepository implements PermissionRepository
{
    /**
     * Finds all entities in the repository.
     *
     * @return array The entities.
     */
    public function findAll()
    {
        return parent::findAll();
    }

    /**
     * Finds a permission based on the permission name.
     *
     * @param  string $permissionName name of the permission we want
     * @return null|Permission
     */
    public function findOneByName(string $permissionName)
    {
        return parent::findOneByName($permissionName);
    }

    /**
     * store a new user in the DB.
     * @param  Permission $permission
     */
    public function save(Permission $permission)
    {
        $this->_em->persist($permission);
        $this->_em->flush();
    }
}
