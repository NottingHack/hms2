<?php

namespace App\Console\Commands\Permissions;

use LaravelDoctrine\ACL\Permissions\Permission;

class AddCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:add
                            {role : Roles to add permissions to}
                            {permission : Permission to add}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add permission to a role. Permission will be created if it does not exist';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (! $role = $this->getRole($this->argument('role'))) {
            return;
        }

        if (! $permission = $this->getPermission($this->argument('permission'), false)) {
            $permission = new Permission($this->argument('permission'));
        }

        if ($role->hasPermissionTo($permission->getName())) {
            $this->info($role->getName().' already has the '.$permission->getName().' permission.');

            return;
        }

        $this->info('Adding '.$permission->getName().' permission to the '.$role->getName().' role:');
        $role->addPermission($permission);
        $this->entityManager->persist($permission);
        $this->entityManager->persist($role);
        $this->entityManager->flush();

        $this->call('permissions:list', [
            'roles' => [$role->getName()],
        ]);
    }
}
