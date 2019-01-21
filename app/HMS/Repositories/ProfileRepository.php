<?php

namespace HMS\Repositories;

use HMS\Entities\Profile;

interface ProfileRepository
{
    /**
     * Save Profile to the DB.
     *
     * @param Profile $profile
     */
    public function save(Profile $profile);
}
