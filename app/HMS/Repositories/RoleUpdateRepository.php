<?php

namespace HMS\Repositories;

use HMS\Entities\User;
use HMS\Entities\RoleUpdate;

interface RoleUpdateRepository
{
    /**
     * @param  $id
     * @return null|Role
     */
    public function find($id);

    /**
     * @param  User   $user
     * @return array
     */
    public function findByUser(User $user);

    /**
     * save RoleUpdate to the DB.
     * @param  RoleUpdate $roleUpdate
     */
    public function save(RoleUpdate $roleUpdate);
}
