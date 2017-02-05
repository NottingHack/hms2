<?php

namespace Database\Migrations;

use HMS\Entities\Role;
use Doctrine\DBAL\Schema\Schema as Schema;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Doctrine\DBAL\Migrations\AbstractMigration;
use LaravelDoctrine\ACL\Permissions\Permission;

class Version20161106154138_add_roles_permissions extends AbstractMigration
{
    /**
     * @var Array
     */
    private $permEntities = [];

    /**
     * @var Array
     */
    private $permStrings = [
            'view'              =>  'profile.view.self',
            'viewall'           =>  'profile.view.all',
            'viewroles'         =>  'role.view.all',
            'editroles'         =>  'role.edit.all',
            'editprofile'       =>  'profile.edit.self',
            'editprofileall'    =>  'profile.edit.all',
        ];

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addViewPermissions();
        $this->addRolePermissions();
        $this->addEditPermissions();

        $this->addMemberRoles();
        $this->AddSuperUserRole();

        EntityManager::flush();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $perms = "'" . implode("','", $this->permStrings) . "'";
        $this->addSql('DELETE FROM permissions WHERE name IN (' . $perms . ')');

        $roles = "'" . Role::MEMBER_APPROVAL . "','" . Role::MEMBER_PAYMENT . "','" . Role::MEMBER_YOUNG . "','" . Role::MEMBER_EX . "','" . Role::MEMBER_CURRENT . "','" . Role::SUPERUSER . "'";
        $this->addSql('DELETE FROM roles WHERE name IN (' . $roles . ')');
    }

    /**
     * Adds view permissions to the database
     */
    private function addViewPermissions()
    {
        $this->permEntities[$this->permStrings['view']] = new Permission($this->permStrings['view']);
        $this->permEntities[$this->permStrings['viewall']] = new Permission($this->permStrings['viewall']);
        EntityManager::persist($this->permEntities[$this->permStrings['view']]);
        EntityManager::persist($this->permEntities[$this->permStrings['viewall']]);
    }

    /**
     * Adds role permissions to the database
     */
    private function addRolePermissions()
    {
        $this->permEntities[$this->permStrings['viewroles']] = new Permission($this->permStrings['viewroles']);
        $this->permEntities[$this->permStrings['editroles']] = new Permission($this->permStrings['editroles']);
        EntityManager::persist($this->permEntities[$this->permStrings['viewroles']]);
        EntityManager::persist($this->permEntities[$this->permStrings['editroles']]);
    }

    /**
     * Adds edit permissions to the database
     */
    private function addEditPermissions()
    {
        $this->permEntities[$this->permStrings['editprofile']] = new Permission($this->permStrings['editprofile']);
        $this->permEntities[$this->permStrings['editprofileall']] = new Permission($this->permStrings['editprofileall']);
        EntityManager::persist($this->permEntities[$this->permStrings['editprofile']]);
        EntityManager::persist($this->permEntities[$this->permStrings['editprofileall']]);
    }

    /**
     * Adds member roles and attached permissions
     */
    private function addMemberRoles()
    {
        $roles = [
            [Role::MEMBER_APPROVAL, 'Awaiting Approval', 'Member awaiting approval'],
            [Role::MEMBER_PAYMENT, 'Awaiting Payment', 'Awaiting standing order payment'],
            [Role::MEMBER_YOUNG, 'Young Hacker', 'Member between 16 and 18'],
            [Role::MEMBER_EX, 'Current Member', 'Current Member'],
            [Role::MEMBER_CURRENT, 'Ex Member', 'Ex Member'],
        ];

        foreach ($roles as $role) {
            $roleEntity = new Role($role[0], $role[1], $role[2]);
            $roleEntity->addPermission($this->permEntities[$this->permStrings['view']]);
            $roleEntity->addPermission($this->permEntities[$this->permStrings['editprofile']]);
            EntityManager::persist($roleEntity);
        }
    }

    /**
     * Adds super user role with permissions
     */
    private function AddSuperUserRole()
    {
        $role = new Role(Role::SUPERUSER, 'Super User', 'Full access to all parts of the system');
        $role->addPermission($this->permEntities[$this->permStrings['view']]);
        $role->addPermission($this->permEntities[$this->permStrings['viewall']]);
        $role->addPermission($this->permEntities[$this->permStrings['viewroles']]);
        $role->addPermission($this->permEntities[$this->permStrings['editroles']]);
        $role->addPermission($this->permEntities[$this->permStrings['editprofile']]);
        $role->addPermission($this->permEntities[$this->permStrings['editprofileall']]);
        EntityManager::persist($role);
    }
}
