<?php

namespace HMS\Repositories;

use HMS\Entities\Profile;

interface ProfileRepository
{
    /**
     * save Profile to the DB.
     * @param  User $user
     */
    public function save(Profile $Profile);
}
