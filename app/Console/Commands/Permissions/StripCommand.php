<?php

namespace App\Console\Commands\Permissions;

class StripCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:strip
                            {role : Role to strip all permissions from}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove all permissions from a role.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (!$role = $this->getRole($this->argument('role'))) {
            return;
        };

        if ($this->confirm('Remove all permissions from the '. $role->getName() .' role? [y|N]')) {
            $role->stripPermissions();
            $this->entityManager->persist($role);
            $this->entityManager->flush();
            $this->info('All permissions removed from ' . $role->getName());
        }
    }
}