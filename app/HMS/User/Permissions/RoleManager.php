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

    public function getFormattedRoleList($sort = true)
    {
        $roles = $this->roleRepository->findAll();

        $formattedRoles = array();

        foreach ($roles as $role) {
            list($category, $name) = explode('.', $role->getName());

            if (!isset($formattedRoles[$category])) {
                $formattedRoles[$category] = [];
            }

            $formattedRoles[$category][] = [
                'id'            =>  $role->getId(),
                'name'          =>  $role->getName(),
                'displayName'   =>  $role->getDisplayName(),
                'description'   =>  $role->getDescription(),
                ];
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
}

?>
