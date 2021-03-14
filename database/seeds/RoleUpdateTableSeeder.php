<?php

use HMS\Entities\Role;
use HMS\Entities\RoleUpdate;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\RoleUpdateRepository;
use Illuminate\Database\Seeder;

class RoleUpdateTableSeeder extends Seeder
{
    /**
     * @var RoleUpdateRepository
     */
    protected $roleUpdateRepository;

    /**
     * @var RoleRoepistory
     */
    protected $roleRepository;

    /**
     * Create a new TableSeeder instance.
     *
     * @param RoleUpdateRepository $roleUpdateRepository
     * @param RoleRepository $roleRepository
     */
    public function __construct(RoleUpdateRepository $roleUpdateRepository, RoleRepository $roleRepository)
    {
        $this->roleUpdateRepository = $roleUpdateRepository;
        $this->roleRepository = $roleRepository;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [Role::MEMBER_PAYMENT, Role::MEMBER_CURRENT, Role::MEMBER_YOUNG, Role::MEMBER_EX];
        foreach ($roles as $roleName) {
            $role = $this->roleRepository->findOneByName($roleName);
            foreach ($role->getUsers() as $user) {
                $roleUpdate = new RoleUpdate($user, $role);
                $this->roleUpdateRepository->save($roleUpdate);
            }
        }
    }
}
