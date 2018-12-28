<?php

namespace HMS\Repositories;

use HMS\Entities\Role;
use Doctrine\Common\Collections\ArrayCollection;

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
     * @return Role|null
     */
    public function findOneByName(string $roleName);

    /**
     * @param  string $email
     * @return Role|null
     */
    public function findOneByEmail(string $email);

    /**
     * store a new user in the DB.
     * @param  Role $role
     */
    public function save(Role $role);

    /**
     * Remove a role based on the role name.
     *
     * @param string $toleName
     */
    public function removeOneByName(string $roleName);
}
