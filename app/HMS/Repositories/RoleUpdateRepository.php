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
     * @param Role $role
     *
     * @return RoleUpdate[]
     */
    public function findByRole(Role $role);

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
     * @param User $user
     * @param int $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateByUser(User $user, $perPage = 15, $pageName = 'page');

    /**
     * Save RoleUpdate to the DB.
     *
     * @param RoleUpdate $roleUpdate
     */
    public function save(RoleUpdate $roleUpdate);
}
