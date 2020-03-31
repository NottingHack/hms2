<?php

namespace App\Jobs\GateKeeper;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use HMS\Repositories\MetaRepository;
use HMS\Repositories\RoleRepository;
use HMS\User\Permissions\RoleManager;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use HMS\Repositories\RoleUpdateRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class TemporaryAccessPurgeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @param RoleManager $roleManager
     * @param RoleRepository $roleRepository
     * @param RoleUpdateRepository $roleUpdateRepository
     * @param MetaRepository $metaRepository
     *
     * @return void
     */
    public function handle(
        RoleManager $roleManager,
        RoleRepository $roleRepository,
        RoleUpdateRepository $roleUpdateRepository,
        MetaRepository $metaRepository
    ) {
        $resetUserCount = 0;
        $tempAccessRole = $roleRepository->findOneByName('team.tempAccess');

        if (is_null($tempAccessRole)) {
            return;
        }

        $resetCutoffDate = Carbon::now();
        $resetCutoffDate->sub(
            CarbonInterval::instance(
                new \DateInterval($metaRepository->get('temp_access_reset_interval', 'PT12H'))
            )
        );

        foreach ($tempAccessRole->getUsers() as $user) {
            $roleAddedRecord = $roleUpdateRepository->findLatestRoleAddedByUser($tempAccessRole, $user);
            if ($roleAddedRecord->getCreatedAt()->isBefore($resetCutoffDate)) {
                // role was granted before cutoff date, need to remove it
                $roleManager->removeUserFromRole($user, $tempAccessRole);
                $resetUserCount++;
            }
        }

        if ($resetUserCount) {
            Log::info('TemporaryAccessPurgeJob: Removed temporary access for ' . $resetUserCount . ' users.');
        }
    }
}
