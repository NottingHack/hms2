<?php

namespace HMS\Repositories;

use Carbon\Carbon;

interface RoleRepository
{
    /**
     * Finds all entities in the repository.
     *
     * @return ArrayCollection The entities.
     */
    public function findAll();

    /**
     * Finds a role based on the role name.
     *
     * @param  string $roleName name of the role we want
     * @return Role|object
     */
    public function findByName(string $roleName);

    /**
     * store a new user in the DB.
     * @param  Role $role
     */
    public function save($role);
}
