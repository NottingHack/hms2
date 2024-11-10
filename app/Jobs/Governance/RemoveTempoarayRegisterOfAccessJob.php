<?php

namespace App\Jobs\Governance;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use HMS\Entities\Role;
use HMS\Repositories\MetaRepository;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\RoleUpdateRepository;
use HMS\User\Permissions\RoleManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RemoveTempoarayRegisterOfAccessJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(
        RoleRepository $roleRepository,
        RoleUpdateRepository $roleUpdateRepository,
        RoleManager $roleManager,
        MetaRepository $metaRepository
    ): void {
        $roleNames = [
            Role::TEMPORARY_VIEW_REGISTER_OF_MEMBERS,
            Role::TEMPORARY_VIEW_REGISTER_OF_DIRECTORS,
        ];
        $temporaryRegisterViewPeriod = $metaRepository->get('temporary_register_view_period', 'P7D');
        $cutoffDate = Carbon::today()->sub(CarbonInterval::create($temporaryRegisterViewPeriod));

        foreach ($roleNames as $roleName) {
            $role = $roleRepository->findOneByName($roleName);
            foreach ($role->getUsers() as $user) {
                $roleUpdate = $roleUpdateRepository->findLatestRoleAddedByUser($role, $user);
                if ($roleUpdate->getCreatedAt()->isBefore($cutoffDate)) {
                    $roleManager->removeUserFromRole($user, $role);
                }
            }
        }
    }
}
