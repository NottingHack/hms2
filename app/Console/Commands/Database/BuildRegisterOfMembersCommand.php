<?php

namespace App\Console\Commands\Database;

use HMS\Entities\Role;
use HMS\Factories\Governance\RegisterOfMembersFactory;
use HMS\Repositories\Governance\RegisterOfMembersRepository;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\RoleUpdateRepository;
use Illuminate\Console\Command;

class BuildRegisterOfMembersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hms:database:build-register-of-members';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build Register Of Members from Role Updates';

    /**
     * Execute the console command.
     */
    public function handle(
        RegisterOfMembersFactory $registerOfMembersFactory,
        RegisterOfMembersRepository $registerOfMembersRepository,
        RoleRepository $roleRepository,
        RoleUpdateRepository $roleUpdateRepository,
    ) {
        $role = $roleRepository->findOneByName(Role::MEMBER_CURRENT);

        $roleUpdates = $roleUpdateRepository->findByRole($role);

        foreach ($roleUpdates as $roleUpdate) {
            $user = $roleUpdate->getUser();

            if ($roleUpdate->getRoleAdded()) {
                $_registerOfMember = $registerOfMembersFactory->create(
                    $user,
                    $user->getFirstname(),
                    $user->getLastname(),
                    $roleUpdate->getCreatedAt(),
                );

                $registerOfMembersRepository->save($_registerOfMember);
            } elseif ($roleUpdate->getRoleRemoved()) {
                $registerOfMember = $registerOfMembersRepository->findCurrentByUser($user);

                if (is_null($registerOfMember)) {
                    $this->error('Failed to find current Register Of Member for ' . $user->getFullname() . ' roleUpdate:' . $roleUpdate->getId());
                }

                $registerOfMember->setEndedAt($roleUpdate->getCreatedAt());

                $registerOfMembersRepository->save($registerOfMember);
            }
        }
    }
}
