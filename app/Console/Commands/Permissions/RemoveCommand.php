<?php

namespace App\Console\Commands\Permissions;

class RemoveCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:remove
                            {role : Role to update}
                            {permission : Permission to remove}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove a permission from a role.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ( ! $role = $this->getRole($this->argument('role'))) {
            return;
        }

        if ( ! $permission = $this->getPermission($this->argument('permission'), true)) {
            return;
        }

        if ( ! $role->hasPermissionTo($permission->getName())) {
            $this->info($role->getName() . " doesn't have the " . $permission->getName() . ' permission.');

            return;
        }
        $this->info('Removing ' . $permission->getName() . ' permission from the ' . $role->getName() . ' role:');
        $role->removePermission($permission);
        $this->entityManager->persist($role);
        $this->entityManager->flush();

        $this->call('permissions:list', [
            'roles' => [$role->getName()],
        ]);
    }
}
