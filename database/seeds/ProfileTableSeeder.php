<?php

use HMS\Entities\Role;
use HMS\Entities\Profile;
use libphonenumber\RegionCode;
use Illuminate\Database\Seeder;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\UserRepository;
use Faker\Generator as FakerGenerator;
use Doctrine\ORM\EntityManagerInterface;
use libphonenumber\NumberParseException;
use Propaganistas\LaravelPhone\PhoneNumber;

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
     * @var FakerGenerator
     */
    protected $faker;

    /**
     * Create a new TableSeeder instance.
     *
     * @param RoleRepository $roleRepository
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     * @param FakerGenerator $faker
     */
    public function __construct(
        RoleRepository $roleRepository,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        FakerGenerator $faker
    ) {
        $this->roleRepository = $roleRepository;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->faker = $faker;
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

                // validate and format phoneNumbers
                $e164 = null;
                do {
                    try {
                        $e164 = PhoneNumber::make($this->faker->phoneNumber, RegionCode::GB)->formatE164();
                    } catch (NumberParseException $e) {
                        //
                    }
                } while ($e164 == null);
                $p->setContactNumber($e164);

                $this->entityManager->persist($p);
            }
        }
        $this->entityManager->flush();
    }
}
