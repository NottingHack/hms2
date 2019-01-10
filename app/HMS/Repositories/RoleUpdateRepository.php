<?php

namespace HMS\Repositories;

use HMS\Entities\Role;
use HMS\Entities\User;
use HMS\Entities\RoleUpdate;

interface RoleUpdateRepository
{
    /**
     * @param  $id
     * @return null|RoleUpdate
     */
    public function findOneById($id);

    /**
     * @param  User   $user
     * @return array
     */
    public function findByUser(User $user);

    /**
     * find the lastest roleUpdate when this User was give the Role.
     * @param  Role  $role
     * @param  User  $user
     * @return null|RoleUpdate
     */
    public function findLatestRoleAddedByUser(Role $role, User $user);

    /**
     * save RoleUpdate to the DB.
     * @param  RoleUpdate $roleUpdate
     */
    public function save(RoleUpdate $roleUpdate);
}
