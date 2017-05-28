<?php

namespace App\Console\Commands\Permissions;

use HMS\Entities\Role;
use Illuminate\Console\Command;
use HMS\Repositories\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use LaravelDoctrine\ACL\Permissions\Permission;

class DefaultsCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:defaults';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restores roles and permissions to the default set. You should probably run migrations beforehand and the seeder afterwards';

    /**
     * @var array
     */
    private $permissions = [];

    /**
     * @var array
     */
    private $roles = [];

    public function __construct(EntityManagerInterface $entityManager, RoleRepository $roleRepository)
    {
        parent::__construct($entityManager, $roleRepository);

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
        $this->info('Permissions created');

        $this->createRoles();
        $this->info('Roles created');

        $this->entityManager->flush();
    }

    /**
     * Creates permissions based on array.
     * @return void
     */
    private function createPermissionEntities()
    {
        foreach ($this->permissions as $permission => &$entity) {
            $entity = new Permission($permission);
            $this->entityManager->persist($entity);
        }
    }

    /**
     * Creates roles and assigns permissions.
     * @return void
     */
    private function createRoles()
    {
        foreach ($this->roles as $roleName => $role) {
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
            unset($roleEntity);
        }
    }
}
