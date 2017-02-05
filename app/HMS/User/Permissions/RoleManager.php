<?php

namespace HMS\User\Permissions;

use HMS\Repositories\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use LaravelDoctrine\ACL\Permissions\Permission;

class RoleManager
{
    private $roleRepository;
    private $permissionRepository;

    /**
     * Create a new RoleManager instance.
     *
     * @param HMS\Repositories\RoleRepository $roleRepository An instance of a role repository
     */
    public function __construct(RoleRepository $roleRepository, EntityManagerInterface $em)
    {
        $this->roleRepository = $roleRepository;
        $this->permissionRepository = $em->getRepository(Permission::class);
    }

    /**
     * Update a specific role.
     *
     * @param int $id The id of the role we want to update
     * @param array $details fields that need updating
     */
    public function updateRole($id, $details)
    {
        $role = $this->roleRepository->findbyId($id);

        if (isset($details['displayName'])) {
            $role->setDisplayName($details['displayName']);
        }

        if (isset($details['description'])) {
            $role->setDescription($details['description']);
        }

        if (isset($details['permissions'])) {
            $role->stripPermissions();
            foreach (array_keys($details['permissions']) as $permissionName) {
                $role->addPermission($this->permissionRepository->findOneByName($permissionName));
            }
        }

        EntityManager::persist($role);
        EntityManager::flush();
    }

}
