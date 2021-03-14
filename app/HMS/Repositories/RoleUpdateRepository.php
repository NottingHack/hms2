<?php

namespace HMS\Repositories;

use HMS\Entities\Role;
use HMS\Entities\RoleUpdate;
use HMS\Entities\User;

interface RoleUpdateRepository
{
    /**
     * @param int $id
     *
     * @return null|RoleUpdate
     */
    public function findOneById(int $id);

    /**
     * @param User $user
     *
     * @return array
     */
    public function findByUser(User $user);

    /**
     * Find the lastest roleUpdate when this User was give the Role.
     *
     * @param Role $role
     * @param User $user
     *
     * @return null|RoleUpdate
     */
    public function findLatestRoleAddedByUser(Role $role, User $user);

    /**
     * Save RoleUpdate to the DB.
     *
     * @param RoleUpdate $roleUpdate
     */
    public function save(RoleUpdate $roleUpdate);
}
