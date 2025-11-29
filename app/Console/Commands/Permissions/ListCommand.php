<?php

namespace App\Console\Commands\Permissions;

use HMS\Entities\Role;

class ListCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:list
                            {roles?* : Roles to display permissions, defaults to all roles}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'View permissions structure';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $requestedRoles = $this->argument('roles');
        $roles = [];

        if (empty($requestedRoles)) {
            $roles = $this->roleRepository->findAll()->toArray();
        } else {
            foreach ($requestedRoles as $requestedRole) {
                if (! $role = $this->getRole($requestedRole)) {
                    return;
                }
                $roles[] = $role;
            }
        }

        foreach ($roles as $role) {
            $this->printPermissions($role);
        }
    }

    protected function printPermissions(Role $role)
    {
        $this->info('Permissions for ' . $role->getName() . ':');
        $rolePermissions = $role->getPermissions()->getIterator();
        foreach ($rolePermissions as $permission) {
            $this->line("\t" . $permission->getName());
        }
        $this->line('');
    }
}
