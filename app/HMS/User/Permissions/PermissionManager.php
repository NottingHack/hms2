<?php

namespace HMS\User\Permissions;

use LaravelDoctrine\ORM\Facades\EntityManager;
use LaravelDoctrine\ACL\Permissions\Permission;

class PermissionManager
{
    private $permissionRepository;

    /**
     * Create a new PermissionManager instance.
     */
    public function __construct()
    {
        $this->permissionRepository = EntityManager::getRepository(Permission::class);
    }

    /**
     * Get all permissions and format them to send to a view.
     *
     * @param bool $sort Whether to sort the returned list of permissions or not
     * @return array
     */
    public function getformattedPermissionList($sort = true)
    {
        $permissions = $this->permissionRepository->findAll();

        $formattedPermissions = [];

        foreach ($permissions as $permission) {
            list($category, $name) = explode('.', $permission->getName());

            if ( ! isset($formattedPermissions[$category])) {
                $formattedPermissions[$category] = [];
            }

            $formattedPermissions[$category][] = $this->formatPermission($permission);
        }

        if ($sort) {
            $categories = array_keys($formattedPermissions);
            foreach ($categories as $category) {
                usort($formattedPermissions[$category], function ($a, $b) {
                    return strcmp($a['name'], $b['name']);
                });
            }

            ksort($formattedPermissions);
        }

        return $formattedPermissions;
    }

    /**
     * Format the permission to send to the view.
     *
     * @param \LaravelDoctrine\ACL\Permissions\Permission $permission The permission to format
     * @return array
     */
    private function formatPermission($permission)
    {
        $formattedPermissions = [
                'id'            =>  $permission->getId(),
                'name'          =>  $permission->getName(),
                ];

        return $formattedPermissions;
    }
}
