<?php

namespace HMS\User\Permissions;

use App\Events\Roles\RoleCreated;
use App\Events\Roles\UserAddedToRole;
use App\Events\Roles\UserRemovedFromRole;
use Doctrine\ORM\EntityManagerInterface;
use HMS\Entities\Role;
use HMS\Entities\User;
use HMS\Repositories\PermissionRepository;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\UserRepository;

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
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * Create a new RoleManager instance.
     *
     * @param RoleRepository $roleRepository An instance of a role repository
     * @param PermissionRepository $permissionRepository
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        RoleRepository $roleRepository,
        PermissionRepository $permissionRepository,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->roleRepository = $roleRepository;
        $this->permissionRepository = $permissionRepository;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * Creates roles and assigns permissions.
     *
     * @param string $roleName
     * @param array $role
     * @param array $permissions
     *
     * @return Role new role object
     */
    public function createRoleFromTemplate(string $roleName, array $role, array $permissions)
    {
        $roleEntity = new Role($roleName, $role['name'], $role['description']);
        if (isset($role['email'])) {
            $roleEntity->setEmail($role['email']);
        }
        if (isset($role['slackChannel'])) {
            $roleEntity->setSlackChannel($role['slackChannel']);
        }
        if (isset($role['retained'])) {
            $roleEntity->setRetained($role['retained']);
        }
        if (count($role['permissions']) == 1 && $role['permissions'][0] == '*') {
            foreach ($permissions as $permission) {
                $roleEntity->addPermission($permission);
            }
        } else {
            foreach ($role['permissions'] as $permission) {
                $roleEntity->addPermission($permissions[$permission]);
            }
        }
        $this->roleRepository->save($roleEntity);
        event(new RoleCreated($roleEntity));

        return $roleEntity;
    }

    /**
     * Create a new team Role.
     *
     * @param string $name
     * @param string $displayName
     * @param string $description
     * @param null|string $email
     * @param null|string $slackChannel
     *
     * @return Role new team role object
     */
    public function createTeam(
        string $name,
        string $displayName,
        string $description,
        ?string $email = null,
        ?string $slackChannel = null
    ) {
        $roleEntity = new Role($name, $displayName, $description);

        if (isset($email)) {
            $roleEntity->setEmail($email);
        }
        if (isset($slackChannel)) {
            $roleEntity->setSlackChannel($slackChannel);
        }

        $defaultPermissions = config('roles.defaultTeamPermissions');
        foreach ($defaultPermissions as $permissionName) {
            $roleEntity->addPermission($this->permissionRepository->findOneByName($permissionName));
        }

        $this->roleRepository->save($roleEntity);
        event(new RoleCreated($roleEntity));

        return $roleEntity;
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
            foreach ($details['permissions'] as $permissionName) {
                $role->addPermission($this->permissionRepository->findOneByName($permissionName));
            }
        }

        $this->roleRepository->save($role);
    }

    /**
     * Add user to Role and fire of an event.
     *
     * @param User $user
     * @param Role $role
     */
    public function addUserToRole(User $user, Role $role)
    {
        $user->getRoles()->add($role);
        $this->userRepository->save($user);
        event(new UserAddedToRole($user, $role));
        $this->entityManager->refresh($user);
    }

    /**
     * Add user to Role and fire of an event.
     *
     * @param User $user
     * @param string $roleName take a role name string rather than a role enitity
     */
    public function addUserToRoleByName(User $user, string $roleName)
    {
        if (! $role = $this->roleRepository->findOneByName($roleName)) {
            return;
        }

        $this->addUserToRole($user, $role);
    }

    /**
     * Remove a user from a role and fire of an update event.
     *
     * @param User $user
     * @param Role $role
     */
    public function removeUserFromRole(User $user, Role $role)
    {
        $user->getRoles()->removeElement($role);
        $this->userRepository->save($user);
        event(new UserRemovedFromRole($user, $role));
        $this->entityManager->refresh($user);
    }

    /**
     * Remove a user from a role and fire of an update event.
     *
     * @param User $user
     * @param string $roleName take a role name string rather than a role enitity
     */
    public function removeUserFromRoleByName(User $user, string $roleName)
    {
        if (! $role = $this->roleRepository->findOneByName($roleName)) {
            return;
        }

        $this->removeUserFromRole($user, $role);
    }
}
