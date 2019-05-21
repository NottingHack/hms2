<?php

use Carbon\Carbon;
use HMS\Entities\Role;
use HMS\Entities\User;
use HMS\Auth\PasswordStore;
use Illuminate\Database\Seeder;
use HMS\Repositories\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserTableSeeder extends Seeder
{
    /**
     * @var int
     */
    private $numUsersToCreate = 200;

    /**
     * @var int
     */
    private $proportionCurrentMembers = 2;

    /**
     * @var bool
     */
    private $createAdmin = true;

    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * @var PasswordStore
     */
    protected $passwordStore;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * Create a new TableSeeder instance.
     *
     * @param RoleRepository $roleRepository
     * @param PasswordStore  $passwordStore
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(RoleRepository $roleRepository, PasswordStore $passwordStore, EntityManagerInterface $entityManager)
    {
        $this->roleRepository = $roleRepository;
        $this->passwordStore = $passwordStore;
        $this->entityManager = $entityManager;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // number of current members to create
        $createCurrentMembers = floor($this->numUsersToCreate / $this->proportionCurrentMembers);

        $numLeftToCreate = $this->numUsersToCreate - $createCurrentMembers;

        // split the others equally

        $roles = [Role::MEMBER_APPROVAL, Role::MEMBER_PAYMENT, Role::MEMBER_YOUNG, Role::MEMBER_EX];

        $createOtherUsers = floor($numLeftToCreate / count($roles));

        // any left over? make them current members
        $numLeftToCreate = $numLeftToCreate - (count($roles) * $createOtherUsers);
        $createCurrentMembers += $numLeftToCreate;

        // actually create the current members
        entity(User::class, $createCurrentMembers)
            ->make()
            ->each(function ($u) {
                $u->getRoles()->add($this->roleRepository->findOneByName(Role::MEMBER_CURRENT));
                $this->passwordStore->add($u->getUsername(), 'password');
                $this->entityManager->persist($u);
            });

        // create all the other types
        foreach ($roles as $role) {
            entity(User::class, $createOtherUsers)
                ->make()
                ->each(function ($u) use ($role) {
                    $u->getRoles()->add($this->roleRepository->findOneByName($role));
                    $this->passwordStore->add($u->getUsername(), 'password');
                    $this->entityManager->persist($u);
                });
        }

        // add in the admin user, this will be user $numUsersToCreate + 1;
        if ($this->createAdmin === true) {
            $admin = new User('Admin', 'Admin', 'admin', 'hmsadmin@nottinghack.org.uk');
            $admin->getRoles()->add($this->roleRepository->findOneByName(Role::SUPERUSER));
            $admin->setEmailVerifiedAt(new Carbon);
            $this->passwordStore->add($admin->getUsername(), 'admin');
            $this->entityManager->persist($admin);
        }

        $this->entityManager->flush();
    }
}
