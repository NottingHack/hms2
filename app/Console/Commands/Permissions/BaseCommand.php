<?php

namespace App\Console\Commands\Permissions;

use Hms\Entities\Role;
use Doctrine\ORM\EntityManagerInterface;
use HMS\Repositories\RoleRepository;
use Illuminate\Console\Command;
use LaravelDoctrine\ACL\Permissions\Permission;
use LaravelDoctrine\ACL\Permissions\PermissionManager;

abstract class BaseCommand extends Command
{
    /**
     * @var roleRepository
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
     * @param PermissionManager $permissionManager
     */
    public function __construct(EntityManagerInterface $entityManager, RoleRepository $roleRepository, PermissionManager $permissionManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->roleRepository = $roleRepository;
        $this->permissionManager = $permissionManager;
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
