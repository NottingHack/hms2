<?php

namespace Database\Seeders;

use HMS\Entities\Role;
use HMS\Repositories\RoleRepository;
use HMS\Tools\ToolManager;
use HMS\User\Permissions\RoleManager;
use Illuminate\Database\Seeder;

class ToolTableSeeder extends Seeder
{
    /**
     * @var ToolManager
     */
    protected $toolManager;

    /**
     * @var RoleManager
     */
    protected $roleManager;

    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * Create a new TableSeeder instance.
     *
     * @param ToolManager $toolManager
     * @param RoleManager $roleManager
     * @param RoleRepository $roleRepository
     */
    public function __construct(
        ToolManager $toolManager,
        RoleManager $roleManager,
        RoleRepository $roleRepository
    ) {
        $this->toolManager = $toolManager;
        $this->roleManager = $roleManager;
        $this->roleRepository = $roleRepository;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tools = [
            [
                'name' => 'Laser',
                'displayName' => 'A0 Laser',
                'restricted' => true,
                'pph' => 300,
                'bookingLength' => 30,
                'lengthMax' => 120,
                'bookingsMax' => 1,
                'slackChannel' => '#laser',
            ],
            [
                'name' => 'Ultimaker',
                'displayName' => 'Ultimaker',
                'restricted' => true,
                'pph' => 0,
                'bookingLength' => 60,
                'lengthMax' => 240,
                'bookingsMax' => 2,
                'slackChannel' => '#3d-printing',
            ],
            [
                'name' => 'Embroidery Machine',
                'displayName' => 'Embroidery Machine',
                'restricted' => false,
                'pph' => 0,
                'bookingLength' => 15,
                'lengthMax' => 120,
                'bookingsMax' => 1,
                'slackChannel' => '#embroidery-machine',
            ],
        ];

        foreach ($tools as $toolSettings) {
            $tool = $this->toolManager->create(
                $toolSettings['name'],
                $toolSettings['displayName'],
                $toolSettings['restricted'],
                $toolSettings['pph'],
                $toolSettings['bookingLength'],
                $toolSettings['lengthMax'],
                $toolSettings['bookingsMax']
            );

            $this->toolManager->enableTool($tool);

            $currentMembers = $this->roleRepository->findOneByName(Role::MEMBER_CURRENT)->getUsers();
            $exMembers = $this->roleRepository->findOneByName(Role::MEMBER_EX)->getUsers();
            $superusers = $this->roleRepository->findOneByName(Role::SUPERUSER)->getUsers();

            $roleMaintainer = $this->roleRepository->findOneByName('tools.' . $tool->getPermissionName() . '.maintainer');
            $roleInductor = $this->roleRepository->findOneByName('tools.' . $tool->getPermissionName() . '.inductor');
            $roleUser = $this->roleRepository->findOneByName('tools.' . $tool->getPermissionName() . '.user');

            // add 2-5 maintainers
            for ($i = 0; $i < rand(1, 5); $i++) {
                $user = $currentMembers->get(array_rand($currentMembers->toArray()));
                $this->roleManager->addUserToRole($user, $roleUser);
                $this->roleManager->addUserToRole($user, $roleMaintainer);

                //  of witch half are also inductors
                if ((($i + 1) % 2) == 1) {
                    $this->roleManager->addUserToRole($user, $roleInductor);
                }

                $currentMembers->removeElement($user);
            }

            // add 2-5 more inductors
            for ($i = 0; $i < rand(1, 5); $i++) {
                $user = $currentMembers->get(array_rand($currentMembers->toArray()));
                $this->roleManager->addUserToRole($user, $roleUser);
                $this->roleManager->addUserToRole($user, $roleInductor);

                $currentMembers->removeElement($user);
            }

            // add 10-20 users
            for ($i = 0; $i < rand(10, 20); $i++) {
                $user = $currentMembers->get(array_rand($currentMembers->toArray()));
                $this->roleManager->addUserToRole($user, $roleUser);

                $currentMembers->removeElement($user);
            }

            // some ex members also as users
            for ($i = 0; $i < rand(1, $exMembers->count()); $i++) {
                $user = $exMembers->get(array_rand($exMembers->toArray()));
                $this->roleManager->addUserToRole($user, $roleUser);

                $exMembers->removeElement($user);
            }

            foreach ($superusers as $user) {
                $this->roleManager->addUserToRole($user, $roleUser);
                $this->roleManager->addUserToRole($user, $roleInductor);
                $this->roleManager->addUserToRole($user, $roleMaintainer);
            }
        }
    }
}
