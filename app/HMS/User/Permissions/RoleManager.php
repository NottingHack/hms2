<?php

namespace HMS\User\Permissions;

use HMS\Entities\Role;
use HMS\Entities\User;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\UserRepository;
use App\Events\Roles\UserAddedToRole;
use App\Events\Roles\UserRemovedFromRole;
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
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * Create a new RoleManager instance.
     *
     * @param HMS\Repositories\RoleRepository $roleRepository An instance of a role repository
     */
    public function __construct(RoleRepository $roleRepository, PermissionRepository $permissionRepository, UserRepository $userRepository)
    {
        $this->roleRepository = $roleRepository;
        $this->permissionRepository = $permissionRepository;
        $this->userRepository = $userRepository;
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

    /**
     * add user to Role and fire of an event.
     * @param User   $user
     * @param Role   $role
     */
    public function addUserToRole(User $user, Role $role)
    {
        $user->getRoles()->add($role);
        $this->userRepository->save($user);
        event(new UserAddedToRole($user, $role));
    }

    /**
     * add user to Role and fire of an event.
     * @param User   $user
     * @param string $roleName take a role name string rather than a role enitity
     */
    public function addUserToRoleByName(User $user, string $roleName)
    {
        $role = $this->roleRepository->findOneByName($roleName);
        $this->addUserToRole($user, $role);
    }

    /**
     * remove a user from a role and fire of an update event.
     * @param  User   $user
     * @param  Role   $role
     */
    public function removeUserFromRole(User $user, Role $role)
    {
        $user->getRoles()->removeElement($role);
        $this->userRepository->save($user);
        event(new UserRemovedFromRole($user, $role));
    }

    /**
     * remove a user from a role and fire of an update event.
     * @param  User   $user
     * @param string $roleName take a role name string rather than a role enitity
     */
    public function removeUserFromRoleByName(User $user, string $roleName)
    {
        $role = $this->roleRepository->findOneByName($roleName);
        $this->removeUserFromRole($user, $role);
    }
}
