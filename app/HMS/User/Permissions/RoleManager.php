<?php

namespace HMS\User\Permissions;

use HMS\Repositories\RoleRepository;

class RoleManager
{

    private $roleRepository;


    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function getFormattedRoleList()
    {
        $roles = $this->roleRepository->findAll();

    }
}

?>
