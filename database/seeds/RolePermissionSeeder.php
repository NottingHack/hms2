<?php

use HMS\Entities\Role;
use Illuminate\Database\Seeder;
use LaravelDoctrine\ORM\Facades\EntityManager;
use LaravelDoctrine\ACL\Permissions\Permission;

class RolePermissionSeeder extends Seeder
{
    private $permissions = [];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->addViewPermissions();
        $this->addRolePermissions();

        $this->addMemberRoles();
        $this->AddSuperUserRole();

        EntityManager::flush();
    }

    private function addViewPermissions()
    {
        $this->permissions['profile.view.self'] = new Permission('profile.view.self');
        $this->permissions['profile.view.all'] = new Permission('profile.view.all');
        EntityManager::persist($this->permissions['profile.view.self']);
        EntityManager::persist($this->permissions['profile.view.all']);
    }

    private function addRolePermissions()
    {
        $this->permissions['role.view.all'] = new Permission('role.view.all');
        $this->permissions['role.edit.all'] = new Permission('role.edit.all');
        EntityManager::persist($this->permissions['role.view.all']);
        EntityManager::persist($this->permissions['role.edit.all']);
    }

    private function addMemberRoles()
    {
        $roles = [
            ['member.approval', 'Awaiting Approval', 'Member awaiting approval'],
            ['member.payment', 'Awaiting Payment', 'Awaiting standing order payment'],
            ['member.young', 'Young Hacker', 'Member between 16 and 18'],
            ['member.current', 'Current Member', 'Current Member'],
            ['member.ex', 'Ex Member', 'Ex Member'],
        ];

        foreach ($roles as $role) {
            $roleEntity = new Role($role[0], $role[1], $role[2]);
            $roleEntity->addPermission($this->permissions['profile.view.self']);
            EntityManager::persist($roleEntity);
        }
    }

    private function AddSuperUserRole()
    {
        $role = new Role('user.super', 'Super User', 'Full access to all parts of the system');
        $role->addPermission($this->permissions['profile.view.self']);
        $role->addPermission($this->permissions['profile.view.all']);
        $role->addPermission($this->permissions['role.view.all']);
        $role->addPermission($this->permissions['role.edit.all']);
        EntityManager::persist($role);
    }
}
