<?php

namespace HMS\Repositories;

interface UserRepository
{
    public function find($id);

    public function findByUsername($username);

    public function findByEmail($email);

    public function create($user);
}
