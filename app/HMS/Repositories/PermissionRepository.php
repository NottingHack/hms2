<?php

namespace HMS\Repositories;

use LaravelDoctrine\ACL\Permissions\Permission;

interface PermissionRepository
{
    /**
     * Finds all entities in the repository.
     *
     * @return array The entities.
     */
    public function findAll();

    /**
     * Finds a permission based on the permission name.
     *
     * @param string $permissionName name of the permission we want
     *
     * @return null|Permission
     */
    public function findOneByName(string $permissionName);

    /**
     * Store a new user in the DB.
     *
     * @param Permission $permission
     */
    public function save(Permission $permission);
}
