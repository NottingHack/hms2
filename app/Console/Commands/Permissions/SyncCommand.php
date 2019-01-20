<?php

namespace App\Console\Commands\Permissions;

use HMS\Entities\Role;
use Illuminate\Console\Command;
use HMS\Repositories\RoleRepository;
use HMS\User\Permissions\RoleManager;
use Doctrine\ORM\EntityManagerInterface;
use HMS\Repositories\PermissionRepository;
use LaravelDoctrine\ACL\Permissions\Permission;

class SyncCommand extends BaseCommand
{
    /**
     * @var PermissionRepository
     */
    protected $permissionRepository;

    /**
     * @var RoleManager
     */
    protected $roleManager;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync permissions and roles to match the default config';

    /**
     * @var array
     */
    private $permissions = [];

    /**
     * @var array
     */
    private $roles = [];

    public function __construct(EntityManagerInterface $entityManager, RoleRepository $roleRepository, PermissionRepository $permissionRepository, RoleManager $roleManager)
    {
        parent::__construct($entityManager, $roleRepository);
        $this->permissionRepository = $permissionRepository;
        $this->roleManager = $roleManager;

        $permissions = config('roles.permissions');

        foreach ($permissions as $permission) {
            $this->permissions[$permission] = '';
        }

        $this->roles = config('roles.roles');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->createPermissionEntities();
        $this->info('Permissions synced');

        $this->syncRoles();
        $this->info('Roles synced');

        $this->entityManager->flush();
    }

    /**
     * Creates permissions based on array.
     * @return void
     */
    private function createPermissionEntities()
    {
        foreach ($this->permissions as $permission => &$entity) {
            if (! $entity = $this->permissionRepository->findOneByName($permission)) {
                $entity = new Permission($permission);
                $this->entityManager->persist($entity);
                $this->info('Created permission: '.$permission);
            }
        }
    }

    /**
     * For existing roles, strip and rebuild thier permissions.
     * Create any new roles.
     *
     * @return void
     */
    private function syncRoles()
    {
        foreach ($this->roles as $roleName => $role) {
            if ($roleEntity = $this->roleRepository->findOneByName($roleName)) {
                $roleEntity->stripPermissions();
                if (count($role['permissions']) == 1 && $role['permissions'][0] == '*') {
                    foreach ($this->permissions as $permission) {
                        $roleEntity->addPermission($permission);
                    }
                } else {
                    foreach ($role['permissions'] as $permission) {
                        $roleEntity->addPermission($this->permissions[$permission]);
                    }
                }
                $this->info('Updated role: '.$roleName);
            } else {
                $this->createRole($roleName, $role);
            }
        }
    }

    /**
     * Creates roles and assigns permissions.
     *
     * @param string $roleName
     * @param array $role
     */
    private function createRole(string $roleName, array $role)
    {
        $this->roleManager->createRoleFromTemplate($roleName, $role, $this->permissions);
        $this->info('Created role: '.$roleName);
    }
}
