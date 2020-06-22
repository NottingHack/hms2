<?php

namespace App\Jobs\Gatekeeper;

use Carbon\Carbon;
use HMS\Entities\Role;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use HMS\Repositories\MetaRepository;
use HMS\Repositories\RoleRepository;
use HMS\User\Permissions\RoleManager;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use HMS\Repositories\Gatekeeper\TemporaryAccessBookingRepository;

class UpdateTemporaryAccessRoleJob implements ShouldQueue
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
     * Update user.temporaryAccess role for User that are currently booked.
     *
     * @param TemporaryAccessBookingRepository $temporaryAccessBookingRepository
     * @param RoleManager $roleManager
     * @param RoleRepository $roleRepository
     * @param MetaRepository $metaRepository
     *
     * @return void
     */
    public function handle(
        TemporaryAccessBookingRepository $temporaryAccessBookingRepository,
        RoleManager $roleManager,
        RoleRepository $roleRepository,
        MetaRepository $metaRepository
    ) {
        $temporaryAccessRole = $roleRepository->findOneByName(Role::TEMPORARY_ACCESS);

        $currentTemporaryAccessBookings = collect($temporaryAccessBookingRepository->findBetween(
            Carbon::now()->subMinutes($metaRepository->get('temporary_access_rfid_window', 10)),
            Carbon::now()->addMinutes($metaRepository->get('temporary_access_rfid_window', 10))
        ));

        // filter not approved
        $currentTemporaryAccessBookings = $currentTemporaryAccessBookings->filter->isApproved();

        $currentTemporaryAccessUsers = $currentTemporaryAccessBookings->map->getUser();

        // remove any users that are not currently booked
        $resetUserCount = 0;
        foreach ($temporaryAccessRole->getUsers() as $user) {
            if (! $currentTemporaryAccessUsers->contains($user)) {
                $roleManager->removeUserFromRole($user, $temporaryAccessRole);
                $resetUserCount++;
            }
        }

        if ($resetUserCount) {
            Log::info(
                'TemporaryAccessBookingManager@updateTemporaryAccessRole: Removed temporary access for '
                . $resetUserCount . ' users.'
            );
        }

        // add role to any user that does not currently have it
        $addUserCount = 0;
        foreach ($currentTemporaryAccessUsers as $user) {
            if (! $user->hasRole($temporaryAccessRole)) {
                $roleManager->addUserToRole($user, $temporaryAccessRole);
                $addUserCount++;
            }
        }

        if ($addUserCount) {
            Log::info(
                'TemporaryAccessBookingManager@updateTemporaryAccessRole: Added temporary access for '
                . $addUserCount . ' users.'
            );
        }
    }
}
