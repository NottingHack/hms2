<?php

namespace HMS\Repositories\Gatekeeper;

use HMS\Entities\User;
use HMS\Entities\Gatekeeper\Pin;

interface PinRepository
{
    /**
     * @param string $pin
     *
     * @return null|Pin
     */
    public function findOneByPin(string $pin);

    /**
     * @param User $user
     *
     * @return Pin[]
     */
    public function findByUser(User $user);

    /**
     * Save Pin to the DB.
     *
     * @param Pin $pin
     */
    public function save(Pin $pin);
}
