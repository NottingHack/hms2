<?php

namespace App\Jobs\Gatekeeper;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use HMS\Repositories\RoleRepository;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use HMS\Repositories\Gatekeeper\BuildingRepository;
use HMS\Repositories\Gatekeeper\TemporaryAccessBookingRepository;

class TemporaryAcccessCheckZoneOccupancyJob implements ShouldQueue
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
     * @param TemporaryAccessBookingRepository $temporaryAccessBookingRepository
     * @param BuildingRepository $buildingRepository
     * @param RoleRepository $roleRepository
     *
     * @return void
     */
    public function handle(
        TemporaryAccessBookingRepository $temporaryAccessBookingRepository,
        BuildingRepository $buildingRepository,
        RoleRepository $roleRepository
    ) {
        $now = Carbon::now();
        $fiftenMinutesAfter = $now->clone()->addMinutes(10);
        $thirtyMintuesBefore = $now->clone()->subMinutes(30);
        $sixtyMinutesBefore = $now->clone()->subMinutes(60);

        // for each building that is not FULL_OPEN
        $buildings = collect($buildingRepository->findAll())->reject->isFullOpen();

        foreach ($buildings as $building) {
            // Building > Zones > ZoneOccupants > User
            // collect all ZoneOccupants (Users)
            $users = collect($building->getZones())->flatMap(function ($zone) {
                return collect($zone->getZoneOccupancts())->map->getUser();
            });

            foreach ($users as $user) {
                // if a User has a booking now, or due to start in 10 minutes
                //   skip
                // else if User has a booking that ended less than 30 mins (warning period) ago
                //   skip
                // else if User has a booking that ended more that 30 mins (warning period) ago
                //   if have not already notified user
                //     notify user asking if they have left yet
                //     make a note we notified user
                //   if it ended more the 60 mins ago (trustee notify period)
                //     notify trustees
                //     make a note we notified trustees
                // else if User has no past booking
                //   bugger?
                //

                $booking = $temporaryAccessBookingRepository
                    ->latestBeforeDatetimeForBuildingAndUser($fiftenMinutesAfter, $building, $user);

                if (is_null($booking)) {
                    // bugger
                    Log::info(
                        'TACZOJ: User ' . $user->getId()
                        . ' is in the building with out a booking'
                    );
                } elseif (! $booking->isApproved()) {
                    Log::info(
                        'TACZOJ: User ' . $user->getId()
                        . ' is in the building without an approved booking.'
                        . ' BookingId: ' . $booking->getId()
                    );
                } elseif ($booking->getEnd()->isAfter($thirtyMintuesBefore)) {
                    Log::info(
                        'TACZOJ: User ' . $user->getId()
                        . ' is in the building with a current booking or one that ended less than 30 mins ago.'
                        . ' BookingId: ' . $booking->getId()
                    );
                } elseif ($booking->getEnd()->betweenExcluded($sixtyMinutesBefore, $thirtyMintuesBefore)) {
                    Log::info(
                        'TACZOJ: User ' . $user->getId()
                        . ' is in the building 30-60 mins after there booking ended.'
                         . ' BookingId: ' . $booking->getId()
                    );
                } elseif ($booking->getEnd()->isBefore($sixtyMinutesBefore)) {
                    Log::info(
                        'TACZOJ: User ' . $user->getId()
                        . ' is in the building over 60mins after there booking ended.'
                         . ' BookingId: ' . $booking->getId()
                    );
                }
            } // end foreach user
        } // end foreach building
    }
}
