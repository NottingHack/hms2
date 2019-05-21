<?php

use HMS\Entities\Role;
use HMS\Entities\Profile;
use Illuminate\Database\Seeder;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

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

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * Create a new TableSeeder instance.
     *
     * @param RoleRepository $roleRepository
     * @param UserRepository $userRepository,
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(RoleRepository $roleRepository, UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->roleRepository = $roleRepository;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
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
                $this->entityManager->persist($p);
            }
        }
        $this->entityManager->flush();
    }
}
