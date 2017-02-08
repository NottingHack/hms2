<?php

namespace App\Console\Commands\Permissions;

use HMS\Entities\Role;
use Illuminate\Console\Command;
use HMS\Repositories\RoleRepository;
use Illuminate\Support\Facades\Artisan;
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
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * The permissions to set up
     * @var array
     */
    private $permissions = [
        'profile.view.self' =>  '',
        'profile.view.all'  =>  '',
        'role.view.all'     =>  '',
        'role.edit.all'     =>  '',
        'profile.edit.self' =>  '',
        'profile.edit.all'  =>  '',
        'accessCodes.view'  =>  '',
        'meta.view'         =>  '',
        'meta.edit'         =>  '',
    ];

    private $roles = [
        Role::MEMBER_APPROVAL   => [
            'name'          => 'Awaiting Approval',
            'description'   => 'Member awaiting approval',
            'permissions'   => [
                'profile.view.self',
                'profile.edit.self',
            ],
        ],
        Role::MEMBER_PAYMENT    => [
            'name'          => 'Awaiting Payment',
            'description'   => 'Awaiting standing order payment',
            'permissions'   => [
                'profile.view.self',
                'profile.edit.self',
            ],
        ],
        Role::MEMBER_YOUNG      => [
            'name'          => 'Young Hacker',
            'description'   => 'Member between 16 and 18',
            'permissions'   => [
                'profile.view.self',
                'profile.edit.self',
            ],
        ],
        Role::MEMBER_EX         => [
            'name'          => 'Ex Member',
            'description'   => 'Ex Member',
            'permissions'   => [
                'profile.view.self',
                'profile.edit.self',
            ],
        ],
        Role::MEMBER_CURRENT    => [
            'name'          => 'Current Member',
            'description'   => 'Current Member',
            'permissions'   => [
                'profile.view.self',
                'profile.edit.self',
            ],
        ],
        Role::SUPERUSER         => [
            'name'          => 'Super User',
            'description'   => 'Full access to all parts of the system',
            'permissions'   =>  [
                '*',
            ],
        ],
    ];

    /*public function __construct(EntityManagerInterface $entityManager, RoleRepository $roleRepository)
    {
        $permissions = config('roles.permissions');
        //dd($permissions);
        foreach ($permissions as $permission) {
            $this->permissions[$permissions] = '';
        }

        $this->roles = config('roles.roles');

        parent::__construct($entityManager, $roleRepository);
    }*/

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Artisan::call('doctrine:migrations:refresh');
        // $this->info('Database reset and migrations run');

        $this->createPermissionEntities();
        $this->info('Permissions created');

        $this->createRoles();
        $this->info('Roles created');

        $this->entityManager->flush();

        // Artisan::call('db:seed');
        // $this->info('Database seeded');
    }

    /**
     * Creates permissions based on array
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
     * Creates roles and assigns permissions
     * @return void
     */
    private function createRoles()
    {
        foreach ($this->roles as $roleName => $role) {
            $roleEntity = new Role($roleName, $role['name'], $role['description']);
            if (count($role['permissions']) == 1 and $role['permissions'][0] == '*') {
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
