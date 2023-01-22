<?php

namespace Database\Seeders;

use HMS\Entities\Role;
use HMS\Factories\Gatekeeper\PinFactory;
use HMS\Repositories\Gatekeeper\PinRepository;
use HMS\Repositories\RoleRepository;
use Illuminate\Database\Seeder;

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

    /**
     * Create a new TableSeeder instance.
     *
     * @param RoleRepository $roleRepository
     * @param PinFactory     $pinFactory
     * @param PinRepository  $pinRepository
     */
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
