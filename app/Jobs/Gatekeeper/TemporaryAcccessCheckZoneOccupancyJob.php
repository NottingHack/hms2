<?php

namespace App\Jobs\Gatekeeper;

use Carbon\Carbon;
use HMS\Entities\User;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use HMS\Repositories\RoleRepository;
use HMS\Entities\Gatekeeper\Building;
use Illuminate\Support\Facades\Cache;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use HMS\Entities\Gatekeeper\TemporaryAccessBooking;
use HMS\Repositories\Gatekeeper\BuildingRepository;
use HMS\Repositories\Gatekeeper\TemporaryAccessBookingRepository;

class TemporaryAcccessCheckZoneOccupancyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Warnings stored in cache as TACZOJ.warnings.
     *
     * [
     *     buildingId => [
     *         userId => [
     *             bookingId => int|null
     *             userNotifiedAt => Carbon|null
     *             trusteesNotifiedAt => Carbon|null
     *         ],
     *     ],
     * ]
     *
     * @var Illuminate\Support\Collection
     */
    protected $warnings;

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
        $this->warnings = Cache::rememberForever('TACZOJ.warnings', function () {
            return [];
        });

        $now = Carbon::now();
        $fiftenMinutesAfter = $now->clone()->addMinutes(10);
        $thirtyMintuesBefore = $now->clone()->subMinutes(30);
        $sixtyMinutesBefore = $now->clone()->subMinutes(60);

        // for each building that is not FULL_OPEN
        $buildings = collect($buildingRepository->findAll())->reject->isFullOpen();

        foreach ($buildings as $building) {
            if (! isset($this->warnings[$building->getId()])) {
                $this->warnings[$building->getId()] = [];
            }

            // Building > Zones > ZoneOccupants > User
            // collect all ZoneOccupants (Users)
            $users = collect($building->getZones())->flatMap(function ($zone) {
                return collect($zone->getZoneOccupancts())->map->getUser();
            });

            $this->removeOldWarnings($building, $users);

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
                    $this->warnings[$building->getId()][$user->getId()] = [
                        'bookingId' => null,
                        'userNotifiedAt' => null,
                        'trusteesNotifiedAt' => null,
                    ];
                } elseif (! $booking->isApproved()) {
                    Log::info(
                        'TACZOJ: User ' . $user->getId()
                        . ' is in the building without an approved booking.'
                        . ' BookingId: ' . $booking->getId()
                    );

                    $this->warnings[$building->getId()][$user->getId()] = [
                        'bookingId' => $booking->getId(),
                        'userNotifiedAt' => null,
                        'trusteesNotifiedAt' => null,
                    ];
                } elseif ($booking->getEnd()->isAfter($thirtyMintuesBefore)) {
                    Log::info(
                        'TACZOJ: User ' . $user->getId()
                        . ' is in the building with a current booking or one that ended less than 30 minutes ago.'
                        . ' BookingId: ' . $booking->getId()
                    );
                } elseif ($booking->getEnd()->isBefore($thirtyMintuesBefore)) {
                    Log::info(
                        'TACZOJ: User ' . $user->getId()
                        . ' is in the building 30 minutes after their booking ended.'
                         . ' BookingId: ' . $booking->getId()
                    );
                    if (! $this->checkUserHasBeenWarned($user, $booking)) {
                        // warn
                        Log::warning(
                            'TACZOJ: User ' . $user->getId() . ' your booking ended at '
                            . $booking->getEnd()->toDateTimeString() . ' have you left yet'
                        );
                    } else {
                        Log::info('User already warned');
                    }

                    if ($booking->getEnd()->isBefore($sixtyMinutesBefore)) {
                        Log::info(
                            'TACZOJ: User ' . $user->getId()
                            . ' is in the building over 60 minutes after their booking ended.'
                             . ' BookingId: ' . $booking->getId()
                        );
                        if (! $this->checkTrusteesHaveBeenNotified($user, $booking)) {
                            // warn
                            Log::warning(
                                'TACZOJ: Trustees User ' . $user->getId()
                                . ' has not yet left though their booking ended at '
                                . $booking->getEnd()->toDateTimeString()
                            );
                        } else {
                            Log::info('Trustees already warned');
                        }
                    }
                }
            } // end foreach user
        } // end foreach building

        Cache::forever('TACZOJ.warnings', $this->warnings);
    }

    /**
     * Check if a user has already been warned about over staying.
     * If not remember them.
     *
     * @param User $user
     * @param TemporaryAccessBooking $booking
     *
     * @return bool
     */
    protected function checkUserHasBeenWarned(User $user, TemporaryAccessBooking $booking)
    {
        $buildingId = $booking->getBookableArea()->getBuilding()->getId();
        if (isset($this->warnings[$buildingId][$user->getId()])
            && $this->warnings[$buildingId][$user->getId()]['userNotifiedAt']) {
            return true;
        }

        $this->warnings[$buildingId][$user->getId()] = [
            'bookingId' => $booking->getId(),
            'userNotifiedAt' => Carbon::now(),
            'trusteesNotifiedAt' => null,
        ];

        return false;
    }

    /**
     * Check if a user has already been warned about over staying.
     * If not remember them.
     *
     * @param User $user
     * @param TemporaryAccessBooking $booking
     *
     * @return bool
     */
    protected function checkTrusteesHaveBeenNotified(User $user, TemporaryAccessBooking $booking)
    {
        $buildingId = $booking->getBookableArea()->getBuilding()->getId();
        if (isset($this->warnings[$buildingId][$user->getId()])) {
            if (is_null($this->warnings[$buildingId][$user->getId()]['trusteesNotifiedAt'])) {
                $this->warnings[$buildingId][$user->getId()]['trusteesNotifiedAt'] = Carbon::now();

                return false;
            }

            return true;
        }

        // should not really get here but just in case
        $this->warnings[$buildingId][$user->getId()] = [
            'bookingId' => $booking->getId(),
            'userNotifiedAt' => null,
            'trusteesNotifiedAt' => Carbon::now(),
        ];

        return false;
    }

    /**
     * Remove Old Warnings for users that are no longer in the building.
     *
     * @param Building   $building
     * @param Collection $users
     *
     * @return void
     */
    protected function removeOldWarnings(Building $building, Collection $users)
    {
        $warnings = collect($this->warnings[$building->getId()]);
        $warningsCount = $warnings->count();
        $userIds = $users->map->getId();
        $this->warnings[$building->getId()] = $warnings->filter(function ($value, $key) use ($userIds) {
            return $userIds->contains($key);
        })->toArray();
        $removedCount = $warningsCount - count($this->warnings[$building->getId()]);

        if ($removedCount) {
            Log::info('TACZOJ: Removed ' . $removedCount . ' old warnings for ' . $building->getName());
        }
    }
}
