<?php

namespace HMS\User\Permissions;

use HMS\Entities\Role;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\PermissionRepository;

class RoleManager
{
    /**
     * @var RoleRepository
     */
    private $roleRepository;

    /**
     * @var PermissionRepository
     */
    private $permissionRepository;

    /**
     * Create a new RoleManager instance.
     *
     * @param HMS\Repositories\RoleRepository $roleRepository An instance of a role repository
     */
    public function __construct(RoleRepository $roleRepository, PermissionRepository $permissionRepository)
    {
        $this->roleRepository = $roleRepository;
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * Update a specific role.
     *
     * @param Role $role the Role we want to update
     * @param array $details fields that need updating
     */
    public function updateRole(Role $role, $details)
    {
        if (isset($details['displayName'])) {
            $role->setDisplayName($details['displayName']);
        }

        if (isset($details['description'])) {
            $role->setDescription($details['description']);
        }

        if (isset($details['email'])) {
            $role->setEmail($details['email']);
        }

        if (isset($details['slackChannel'])) {
            $role->setSlackChannel($details['slackChannel']);
        }

        if (isset($details['retained'])) {
            $role->setRetained(true);
        } else {
            $role->setRetained(false);
        }

        if (isset($details['permissions'])) {
            $role->stripPermissions();
            foreach (array_keys($details['permissions']) as $permissionName) {
                $role->addPermission($this->permissionRepository->findOneByName($permissionName));
            }
        }

        $this->roleRepository->save($role);
    }
}
