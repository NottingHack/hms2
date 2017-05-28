<?php

use HMS\Entities\Role;
use Illuminate\Database\Seeder;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\UserRepository;
use HMS\Factories\Banking\AccountFactory;
use HMS\Repositories\Banking\AccountRepository;

class AccountTableSeeder extends Seeder
{
    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var AccountFactory
     */
    protected $accountFactory;

    /**
     * @var AccountRepository
     */
    protected $accountRepository;

    public function __construct(RoleRepository $roleRepository, UserRepository $userRepository, AccountFactory $accountFactory, AccountRepository $accountRepository)
    {
        $this->roleRepository = $roleRepository;
        $this->userRepository = $userRepository;
        $this->accountFactory = $accountFactory;
        $this->accountRepository = $accountRepository;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [Role::MEMBER_CURRENT, Role::MEMBER_YOUNG, Role::MEMBER_PAYMENT, Role::MEMBER_EX];
        foreach ($roles as $role) {
            $role = $this->roleRepository->findOneByName($role);
            $joint = 0;
            $a = null;
            foreach ($role->getUsers() as $user) {
                if ($joint == 0) {
                    $a = $this->accountFactory->createNewAccount();
                    $joint++;
                } elseif ($joint == 1) {
                    if ($role->getName() == Role::MEMBER_YOUNG) {
                        $a = $this->accountRepository->find(5);
                    }
                    $joint++;
                } else {
                    $a = $this->accountFactory->createNewAccount();
                }
                $user->setAccount($a);
                $this->userRepository->save($user);
            }
        }
    }
}
