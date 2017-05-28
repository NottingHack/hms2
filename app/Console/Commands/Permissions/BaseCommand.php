<?php

namespace App\Console\Commands\Permissions;

use Hms\Entities\Role;
use Illuminate\Console\Command;
use HMS\Repositories\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use LaravelDoctrine\ACL\Permissions\Permission;

abstract class BaseCommand extends Command
{
    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * @var PermissionManager
     */
    protected $permissionManager;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository
     */
    protected $permissionsRepository;

    /**
     * Create a new command instance.
     * @param EntityManagerInterface $entityManager
     * @param RoleRepository $roleRepository
     */
    public function __construct(EntityManagerInterface $entityManager, RoleRepository $roleRepository)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->roleRepository = $roleRepository;
        $this->permissionsRepository = $entityManager->getRepository(Permission::class);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    abstract public function handle();

    /**
     * @param $message
     * @param $error
     */
    protected function printError($message, $error)
    {
        $this->output->block($message, $error, 'fg=white;bg=red', ' ', true);
    }

    /**
     * @param $name
     * @param bool $error
     * @return bool|Role
     */
    protected function getRole($name, $error = true)
    {
        $role = $this->roleRepository->findOneBy(['name' => $name]);
        if ( ! is_null($role)) {
            return $role;
        } else {
            if ($error) {
                $this->printError('The role ' . $name . ' was not found within the system.', 'Incorrect Role');
            }

            return false;
        }
    }

    /**
     * @param $name
     * @param bool $error
     * @return bool|Permission
     */
    protected function getPermission($name, $error = true)
    {
        $permission = $this->permissionsRepository->findOneBy(['name' => $name]);
        if ( ! is_null($permission)) {
            return $permission;
        } else {
            if ($error) {
                $this->printError('The permission ' . $permission . ' was not found within the system.', 'Incorrect Permission');
            }

            return false;
        }
    }
}
