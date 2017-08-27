<?php

namespace HMS\Repositories\GateKeeper;

use HMS\Entities\User;
use HMS\Entities\GateKeeper\Pin;

interface PinRepository
{
    /**
     * @param  string $pin
     * @return null|Pin
     */
    public function findOneByPin(string $pin);

    /**
     * @param  User $user
     * @return Pin[]
     */
    public function findByUser(User $user);

    /**
     * save Pin to the DB.
     * @param  Pin $pin
     */
    public function save(Pin $pin);
}
