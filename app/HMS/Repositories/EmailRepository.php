<?php

namespace HMS\Repositories;

use HMS\Entities\Role;
use HMS\Entities\Email;

// TODO: findByUserPaginate(????);
interface EmailRepository
{
    /**
     * @param  $id
     * @return array
     */
    public function find($id);

    /**
     * @param  Role $role
     * @return array
     */
    public function findByRole(Role $role);

    /**
     * save Email to the DB.
     * @param  Email $email
     */
    public function save(Email $email);
}
