<?php

namespace HMS\Repositories;

use HMS\Entities\Role;
use HMS\Entities\User;
use Doctrine\Common\Collections\ArrayCollection;

interface RoleRepository
{
    /**
     * @param $id
     *
     * @return Role|null
     */
    public function findOneById($id);

    /**
     * Finds all entities in the repository.
     *
     * @return ArrayCollection The entities.
     */
    public function findAll();

    /**
     * Finds a role based on the role name.
     *
     * @param string $roleName name of the role we want
     *
     * @return Role|null
     */
    public function findOneByName(string $roleName);

    /**
     * @param string $email
     *
     * @return Role|null
     */
    public function findOneByEmail(string $email);

    /**
     * Find all the team roles a given user has.
     *
     * @param User $user
     *
     * @return Roles[]
     */
    public function findTeamsForUser(User $user);

    /**
     * Store a new user in the DB.
     *
     * @param Role $role
     */
    public function save(Role $role);

    /**
     * Remove a role based on the role name.
     *
     * @param string $roleName
     */
    public function removeOneByName(string $roleName);
}
