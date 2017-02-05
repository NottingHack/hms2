<?php

namespace HMS\Repositories;

use HMS\Entities\User;

interface UserRepository
{
    public function find($id);

    public function findByUsername(string $username);

    public function findByEmail(string $email);

    public function create(User $user);
}
