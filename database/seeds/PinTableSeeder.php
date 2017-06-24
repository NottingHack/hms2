<?php

use HMS\Entities\Role;
use Illuminate\Database\Seeder;
use HMS\Repositories\RoleRepository;
use HMS\Factories\GateKeeper\PinFactory;
use HMS\Repositories\GateKeeper\PinRepository;

class PinTableSeeder extends Seeder
{
    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * @var PinFactory
     */
    protected $pinFactory;

    /**
     * @var PinRepository
     */
    protected $pinRepository;

    public function __construct(RoleRepository $roleRepository, PinFactory $pinFactory, PinRepository $pinRepository)
    {
        $this->roleRepository = $roleRepository;
        $this->pinFactory = $pinFactory;
        $this->pinRepository = $pinRepository;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [Role::MEMBER_CURRENT, Role::MEMBER_YOUNG, Role::MEMBER_EX];
        foreach ($roles as $role) {
            $role = $this->roleRepository->findOneByName($role);
            foreach ($role->getUsers() as $user) {
                $pin = $this->pinFactory->createNewEnrollPinForUser($user);
                $this->pinRepository->save($pin);
            }
        }
    }
}
