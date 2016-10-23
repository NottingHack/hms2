<?php

use HMS\Entities\User;
use Illuminate\Database\Seeder;
use HMS\Repositories\RoleRepository;
use LaravelDoctrine\ORM\Facades\EntityManager;

class UserTableSeeder extends Seeder
{

    private $numUsersToCreate = 200;

    private $proportionCurrentMembers = 2;

    protected $roleRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
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

        $roles = array('awaitingApproval', 'awaitingPayment', 'youngMember', 'exMember');

        $createOtherUsers = floor($numLeftToCreate / count($roles));

        // any left over? make them current members
        $numLeftToCreate = $numLeftToCreate - (count($roles) * $createOtherUsers);
        $createCurrentMembers += $numLeftToCreate;


        // actually create the current members
        entity(User::class, $createCurrentMembers)
            ->make()
            ->each(function ($u) {
                $u->getRoles()->add($this->roleRepository->findByName('currentMember'));
                EntityManager::persist($u);
            });

        // create all the other types
        foreach ($roles as $role) {
            entity(User::class, $createOtherUsers)
                ->make()
                ->each(function ($u) use ($role) {
                    $u->getRoles()->add($this->roleRepository->findByName($role));
                    EntityManager::persist($u);
                });
        }

        EntityManager::flush();
    }
}
