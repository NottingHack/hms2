<?php

use HMS\Entities\Role;
use HMS\Entities\User;
use HMS\Auth\PasswordStore;
use Illuminate\Database\Seeder;
use HMS\Repositories\RoleRepository;
use LaravelDoctrine\ORM\Facades\EntityManager;

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
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(RoleRepository $roleRepository, PasswordStore $passwordStore)
    {
        $this->roleRepository = $roleRepository;
        $this->passwordStore = $passwordStore;
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
                EntityManager::persist($u);
            });

        // create all the other types
        foreach ($roles as $role) {
            entity(User::class, $createOtherUsers)
                ->make()
                ->each(function ($u) use ($role) {
                    $u->getRoles()->add($this->roleRepository->findOneByName($role));
                    $this->passwordStore->add($u->getUsername(), 'password');
                    EntityManager::persist($u);
                });
        }

        // add in the admin user, this will be user $numUsersToCreate + 1;
        if ($this->createAdmin === true) {
            $admin = new User('Admin', 'Admin', 'admin', 'hmsadmin@nottinghack.org.uk');
            $admin->getRoles()->add($this->roleRepository->findOneByName(Role::SUPERUSER));
            $this->passwordStore->add($admin->getUsername(), 'admin');
            EntityManager::persist($admin);
        }

        EntityManager::flush();
    }
}
