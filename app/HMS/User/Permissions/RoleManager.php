<?php

namespace HMS\User\Permissions;

use HMS\Repositories\RoleRepository;
use LaravelDoctrine\ORM\Facades\EntityManager;
use LaravelDoctrine\ACL\Permissions\Permission;

class RoleManager
{

    private $roleRepository;
    private $permissionRepository;

    /**
     * Create a new RoleManager instance
     *
     * @param HMS\Repositories\RoleRepository $roleRepository An instance of a role repository
     */
    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
        $this->permissionRepository = EntityManager::getRepository(Permission::class);
    }

    /**
     * Get all roles and format them to send to a view
     *
     * @param Boolean $sort Whether to sort the returned list of roles or not
     * @return Array
     */
    public function getFormattedRoleList($sort = true)
    {
        $roles = $this->roleRepository->findAll();

        $formattedRoles = array();

        foreach ($roles as $role) {
            list($category, $name) = explode('.', $role->getName());

            if (!isset($formattedRoles[$category])) {
                $formattedRoles[$category] = [];
            }

            $formattedRoles[$category][] = $this->formatRole($role);
        }

        if ($sort) {
            $categories = array_keys($formattedRoles);
            foreach ($categories as $category) {
                usort($formattedRoles[$category], function ($a, $b) {
                    return strcmp($a["name"], $b["name"]);
                });
            }

            ksort($formattedRoles);
        }

        return $formattedRoles;
    }

    /**
     * Find a role and format it to send to a view
     *
     * @param Integer $id The id of the role we want
     * @return Array
     */
    public function getFormattedRole($id)
    {
        $role = $this->roleRepository->findbyId($id);

        return $this->formatRole($role);
    }

    /**
     * Update a specific role
     *
     * @param Integer $id The id of the role we want to update
     * @param Array $details fields that need updating
     */
    public function updateRole($id, $details)
    {
        $role = $this->roleRepository->findbyId($id);

        //dd($details);

        if (isset($details['displayName'])) {
            $role->setDisplayName($details['displayName']);
        }

        if (isset($details['description'])) {
            $role->setDescription($details['description']);
        }

        if (isset($details['permissions'])) {
            $role->stripPermissions();
            foreach ($details['permissions'] as $permissionName => $null) {
                $role->addPermission($this->permissionRepository->findOneByName($permissionName));
            }
        }

        EntityManager::persist($role);
        EntityManager::flush();
    }

    /**
     * Format the role to send to the view
     *
     * @param \HMS\Entities\Role $role The role to format
     * @return Array
     */
    private function formatRole($role)
    {
        $formattedRole = [
                'id'            =>  $role->getId(),
                'name'          =>  $role->getName(),
                'displayName'   =>  $role->getDisplayName(),
                'description'   =>  $role->getDescription(),
                'permissions'   =>  $this->formatPermissions($role->getPermissions()),
                'users'         =>  $this->formatUsers($role->getUsers()),
                ];

        return $formattedRole;
    }

    /**
     * Format the permissions to send to the view
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $permissions The permissions of the role
     * @return Array
     */
    private function formatPermissions($permissions)
    {
        $formattedPerms = [];

        foreach ($permissions as $permission) {
            $formattedPerms[$permission->getName()] = [
                'id'    =>  $permission->getId(),
                'name'  =>  $permission->getName(),
                ];
        }

        return $formattedPerms;
    }

    /**
     * Format the users to send to the view
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $users The users of the role
     * @return Array
     */
    private function formatUsers($users)
    {
        $formattedUsers = [];

        foreach ($users as $user) {
            $formattedUsers[] = [
                'id'    =>  $user->getId(),
                'name'  =>  $user->getName(),
                ];
        }

        return $formattedUsers;
    }
}

?>
