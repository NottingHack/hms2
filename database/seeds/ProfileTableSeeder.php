<?php

use HMS\Entities\Role;
use HMS\Entities\Profile;
use Illuminate\Database\Seeder;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\UserRepository;
use LaravelDoctrine\ORM\Facades\EntityManager;

class ProfileTableSeeder extends Seeder
{
    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    public function __construct(RoleRepository $roleRepository, UserRepository $userRepository)
    {
        $this->roleRepository = $roleRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [Role::MEMBER_APPROVAL, Role::MEMBER_PAYMENT, Role::MEMBER_YOUNG, Role::MEMBER_EX, Role::MEMBER_CURRENT, Role::SUPERUSER];
        foreach ($roles as $role) {
            $role = $this->roleRepository->findOneByName($role);
            foreach ($role->getUsers() as $user) {
                $p = null;
                switch ($role->getName()) {
                case Role::SUPERUSER:
                    $p = entity(Profile::class, 'superuser')->make(['user' => $user]);
                    break;
                case Role::MEMBER_APPROVAL:
                case Role::MEMBER_PAYMENT:
                    $p = entity(Profile::class, 'approval')->make(['user' => $user]);
                    break;
                case Role::MEMBER_YOUNG:
                    $p = entity(Profile::class, 'youngHacker')->make(['user' => $user]);
                    break;
                default:
                    $p = entity(Profile::class)->make(['user' => $user]);
                    break;
                }
                EntityManager::persist($p);
            }
        }
        EntityManager::flush();
    }
}
