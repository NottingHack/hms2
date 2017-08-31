<?php

namespace App\Console\Commands\Permissions;

use HMS\Entities\Role;
use Illuminate\Console\Command;
use App\Events\Roles\RoleCreated;
use HMS\Repositories\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use HMS\Repositories\PermissionRepository;
use LaravelDoctrine\ACL\Permissions\Permission;

class SyncCommand extends BaseCommand
{
    protected $permissionRepository;

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

    public function __construct(EntityManagerInterface $entityManager, RoleRepository $roleRepository, PermissionRepository $permissionRepository)
    {
        parent::__construct($entityManager, $roleRepository);
        $this->permissionRepository = $permissionRepository;
        
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
            if ( ! $entity = $this->permissionRepository->findOneByName($permission)) {
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
            foreach ($this->permissions as $permission) {
                $roleEntity->addPermission($permission);
            }
        } else {
            foreach ($role['permissions'] as $permission) {
                $roleEntity->addPermission($this->permissions[$permission]);
            }
        }
        $this->entityManager->persist($roleEntity);
        event(new RoleCreated($roleEntity));
        $this->info('Created role: '.$roleName);
        unset($roleEntity);
    }
}
