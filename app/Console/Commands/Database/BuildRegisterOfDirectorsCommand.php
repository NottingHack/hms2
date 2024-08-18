<?php

namespace App\Console\Commands\Database;

use HMS\Entities\Role;
use HMS\Factories\Governance\RegisterOfDirectorsFactory;
use HMS\Repositories\Governance\RegisterOfDirectorsRepository;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\RoleUpdateRepository;
use Illuminate\Console\Command;

class BuildRegisterOfDirectorsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hms:database:build-register-of-directors';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build Register Of Directors from Role Updates';

    /**
     * Execute the console command.
     */
    public function handle(
        RegisterOfDirectorsFactory $registerOfDirectorsFactory,
        RegisterOfDirectorsRepository $registerOfDirectorsRepository,
        RoleRepository $roleRepository,
        RoleUpdateRepository $roleUpdateRepository,
    ) {
        $role = $roleRepository->findOneByName(Role::TEAM_TRUSTEES);

        $roleUpdates = $roleUpdateRepository->findByRole($role);

        foreach ($roleUpdates as $roleUpdate) {
            $user = $roleUpdate->getUser();

            if ($roleUpdate->getRoleAdded()) {
                $_registerOfDirector = $registerOfDirectorsFactory->create(
                    $user,
                    $user->getFirstname(),
                    $user->getLastname(),
                    $user->getProfile()->getAddress1(),
                    $user->getProfile()->getAddress2(),
                    $user->getProfile()->getAddress3(),
                    $user->getProfile()->getAddressCity(),
                    $user->getProfile()->getAddressCounty(),
                    $user->getProfile()->getAddressPostcode(),
                    $roleUpdate->getCreatedAt(),
                );

                $registerOfDirectorsRepository->save($_registerOfDirector);
            } elseif ($roleUpdate->getRoleRemoved()) {
                $registerOfDirector = $registerOfDirectorsRepository->findCurrentByUser($user);

                if (is_null($registerOfDirector)) {
                    $this->error('Failed to find current Register Of Member for ' . $user->getFullname() . ' roleUpdate:' . $roleUpdate->getId());
                }

                $registerOfDirector->setEndedAt($roleUpdate->getCreatedAt());

                $registerOfDirectorsRepository->save($registerOfDirector);
            }
        }
    }
}
