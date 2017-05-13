<?php

namespace HMS\Repositories;

use HMS\Entities\Role;

interface RoleRepository
{
    /**
     * Finds all entities in the repository.
     *
     * @return Doctrine\Common\Collections\ArrayCollection The entities.
     */
    public function findAll();

    /**
     * Finds a role based on the role name.
     *
     * @param  string $roleName name of the role we want
     * @return Role|null
     */
    public function findByName(string $roleName);

    /**
     * @param  string $email
     * @return Role|null
     */
    public function findByOneEmail(string $email);

    /**
     * store a new user in the DB.
     * @param  Role $role
     */
    public function save(Role $role);
}
