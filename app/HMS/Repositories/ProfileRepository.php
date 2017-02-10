<?php

namespace HMS\Repositories;

use HMS\Entities\Profile;

interface ProfileRepository
{
    /**
     * save Profile to the DB.
     * @param  Profile $profile
     */
    public function save(Profile $Profile);
}
