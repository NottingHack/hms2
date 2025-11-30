<?php

namespace App\Console\Commands\Permissions;

use Doctrine\ORM\EntityManagerInterface;
use HMS\Repositories\PermissionRepository;
use HMS\Repositories\RoleRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class CompareCommand extends BaseCommand
{
    /**
     * @var PermissionRepository
     */
    protected $permissionRepository;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:compare';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compare permissions and roles to with the default config';

    /**
     * @var Collection
     */
    private $permissions;

    /**
     * @var Collection
     */
    private $roles;

    /**
     * Construct CompareCommand.
     *
     * @param EntityManagerInterface $entityManager
     * @param RoleRepository $roleRepository
     * @param PermissionRepository $permissionRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        RoleRepository $roleRepository,
        PermissionRepository $permissionRepository
    ) {
        parent::__construct($entityManager, $roleRepository);
        $this->permissionRepository = $permissionRepository;

        $this->permissions = collect(config('roles.permissions'));
        $this->roles = collect(config('roles.roles'));
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Checking Permissions');
        $this->comparePermissionEntities(
            collect($this->permissionRepository->findAll())->map->getName(),
            $this->permissions
        );
        $this->info('Permissions Checked');

        $this->line('');

        $this->compareRoles();
        $this->info('Roles Checked');
    }

    /**
     * Creates permissions based on array.
     *
     * @param Collection $currentPermissions
     * @param Collection $configPermissions
     *
     * @return void
     */
    private function comparePermissionEntities(
        Collection $currentPermissions,
        Collection $configPermissions
    ) {
        $missingFromLive = $configPermissions->diff($currentPermissions)->values();
        $additionalInLive = $currentPermissions->diff($configPermissions)->values();

        $this->info('Permissions missing from database');
        if (count($missingFromLive)) {
            foreach ($missingFromLive as $permission) {
                $this->line($permission);
            }
        } else {
            $this->line('None missing');
        }

        $this->line(''); // $this->newLine(); L8.x

        $this->info('Additional Permissions in the database');

        if (count($additionalInLive)) {
            foreach ($additionalInLive as $permission) {
                $this->line($permission);
            }
        } else {
            $this->line('No additional');
        }
        $this->line(''); // $this->newLine(); L8.x
    }

    /**
     * For existing roles, strip and rebuild their permissions.
     * Create any new roles.
     *
     * @return void
     */
    private function compareRoles()
    {
        foreach ($this->roles as $roleName => $role) {
            if ($roleEntity = $this->roleRepository->findOneByName($roleName)) {
                $this->info('Checking Permissions for Role ' . $roleName);

                if (count($role['permissions']) == 1 && $role['permissions'][0] == '*') {
                    $this->comparePermissionEntities(
                        collect($roleEntity->getPermissions())->map->getName(),
                        $this->permissions
                    );
                } else {
                    $this->comparePermissionEntities(
                        collect($roleEntity->getPermissions())->map->getName(),
                        collect($role['permissions'])
                    );
                }
            } else {
                $this->error('Role ' . $roleName . ' not in the database;');
            }
            $this->line(''); // $this->newLine(); L8.x
        }

        $this->info('Additional Roles in the database');
        $allRoles = collect($this->roleRepository->findAll())->map->getName();
        $additionalRoles = $allRoles->diff($this->roles->keys());

        if (count($additionalRoles)) {
            foreach ($additionalRoles as $roleName) {
                $this->line($roleName);
            }
        } else {
            $this->line('No additional');
        }
        $this->line(''); // $this->newLine(); L8.x
    }
}
