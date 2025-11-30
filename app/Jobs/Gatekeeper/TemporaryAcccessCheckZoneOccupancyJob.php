<?php

namespace App\Jobs\Gatekeeper;

use App\Notifications\Gatekeeper\NotifyTrusteeOverstay;
use App\Notifications\Gatekeeper\NotifyUserOverstay;
use Carbon\Carbon;
use HMS\Entities\Gatekeeper\Building;
use HMS\Entities\Gatekeeper\TemporaryAccessBooking;
use HMS\Entities\Role;
use HMS\Entities\User;
use HMS\Repositories\Gatekeeper\BuildingRepository;
use HMS\Repositories\Gatekeeper\TemporaryAccessBookingRepository;
use HMS\Repositories\MetaRepository;
use HMS\Repositories\RoleRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Laravel\Horizon\Contracts\Silenced;

class TemporaryAcccessCheckZoneOccupancyJob implements ShouldQueue, Silenced
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
     * @var Collection
     */
    protected $warnings;

    /**
     * @var Role
     */
    protected $trusteesTeamRole;

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
     * @param MetaRepository $metaRepository
     *
     * @return void
     */
    public function handle(
        TemporaryAccessBookingRepository $temporaryAccessBookingRepository,
        BuildingRepository $buildingRepository,
        RoleRepository $roleRepository,
        MetaRepository $metaRepository
    ) {
        $this->warnings = Cache::rememberForever('TACZOJ.warnings', function () {
            return [];
        });

        $now = Carbon::now();
        $bookingSearchLimit = $now->clone()->addMinutes($metaRepository->getInt('temporary_access_rfid_window', 10));
        $userLimit = $now->clone()->subMinutes($metaRepository->getInt('temporary_access_uesr_notification', 30));
        $trusteeLimit = $now->clone()->subMinutes($metaRepository->getInt('temporary_access_trustee_notification', 120));

        $this->trusteesTeamRole = $roleRepository->findOneByName(Role::TEAM_TRUSTEES);

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
                if ($user->hasRoleByName(Role::BUILDING_ACCESS)) {
                    continue;
                }

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
                    ->latestBeforeDatetimeForBuildingAndUser($bookingSearchLimit, $building, $user);

                if (is_null($booking)) {
                    // User has no past booking
                    $this->noBooking($building, $user);
                } elseif (! $booking->isApproved()) {
                    // User booking is not approved
                    $this->unapprovedBooking($building, $user, $booking);
                } elseif ($booking->getEnd()->isAfter($userLimit)) {
                    // User has a booking now, or due to start in 10 minutes
                    $this->currentOrFutureBooking($building, $user, $booking);
                } elseif ($booking->getEnd()->isBefore($userLimit)) {
                    // User has a booking that ended more than user limit ago
                    $this->afterUserLimit($building, $user, $booking);

                    if ($booking->getEnd()->isBefore($trusteeLimit)) {
                        // User has a booking that ended more than trustee limit ago
                        $this->afterTrusteeLimit($building, $user, $booking);
                    }
                }
            } // end foreach user
        } // end foreach building

        Cache::forever('TACZOJ.warnings', $this->warnings);
    }

    /**
     * User is in the building with out ever having a booking.
     *
     * @param Building               $building
     * @param User                   $user
     *
     * @return void
     */
    protected function noBooking(Building $building, User $user)
    {
        // bugger
        Log::info(
            'TACZOJ: User ' . $user->getId()
            . ' is in the building with out a booking'
        );

        // $this->warnings[$building->getId()][$user->getId()] = [
        //     'bookingId' => null,
        //     'userNotifiedAt' => null,
        //     'trusteesNotifiedAt' => null,
        // ];
    }

    /**
     * User is in the building but booking is unapproved.
     *
     * @param Building               $building
     * @param User                   $user
     * @param TemporaryAccessBooking $booking
     *
     * @return void
     */
    protected function unapprovedBooking(Building $building, User $user, TemporaryAccessBooking $booking)
    {
        // bugger
        Log::info(
            'TACZOJ: User ' . $user->getId()
            . ' is in the building without an approved booking.'
            . ' BookingId: ' . $booking->getId()
        );

        // $this->warnings[$building->getId()][$user->getId()] = [
        //     'bookingId' => $booking->getId(),
        //     'userNotifiedAt' => null,
        //     'trusteesNotifiedAt' => null,
        // ];
    }

    /**
     * User is in the building with am approved current, future or recently ended booking.
     *
     * @param Building               $building
     * @param User                   $user
     * @param TemporaryAccessBooking $booking
     *
     * @return void
     */
    protected function currentOrFutureBooking(Building $building, User $user, TemporaryAccessBooking $booking)
    {
        Log::info(
            'TACZOJ: User ' . $user->getId()
            . ' is in the building with a current booking or one that ended less than user limit ago.'
            . ' BookingId: ' . $booking->getId()
        );

        // remove any old warning tracking for this user
        unset($this->warnings[$building->getId()][$user->getId()]);
    }

    /**
     * User is in the building past the end of their Booking end user limit.
     *
     * @param Building               $building
     * @param User                   $user
     * @param TemporaryAccessBooking $booking
     *
     * @return void
     */
    protected function afterUserLimit(Building $building, User $user, TemporaryAccessBooking $booking)
    {
        Log::info(
            'TACZOJ: User ' . $user->getId()
            . ' is in the building user limit after their booking ended.'
             . ' BookingId: ' . $booking->getId()
        );

        $buildingId = $building->getId();

        if (isset($this->warnings[$buildingId][$user->getId()])
            && $this->warnings[$buildingId][$user->getId()]['userNotifiedAt']) {
            // nothing to do
            Log::info('User already warned');

            return;
        }

        // Notify the user
        Log::warning(
            'TACZOJ: User ' . $user->getId() . ' your booking ended at '
            . $booking->getEnd()->toDateTimeString() . ' have you left yet'
        );

        $user->notify(new NotifyUserOverstay($building, $booking));

        // and remember we have sent a warning
        if (! isset($this->warnings[$buildingId][$user->getId()])) {
            // no warning for this user yet, add a new one
            $this->warnings[$buildingId][$user->getId()] = [
                'bookingId' => $booking->getId(),
                'userNotifiedAt' => Carbon::now(),
                'trusteesNotifiedAt' => null,
            ];
        } else {
            $this->warnings[$buildingId][$user->getId()]['userNotifiedAt'] = Carbon::now();
        }
    }

    /**
     * User is in the building past the end of the Booking end trustee limit.
     *
     * @param Building               $building
     * @param User                   $user
     * @param TemporaryAccessBooking $booking
     *
     * @return void
     */
    protected function afterTrusteeLimit(Building $building, User $user, TemporaryAccessBooking $booking)
    {
        Log::info(
            'TACZOJ: User ' . $user->getId()
            . ' is in the building over trustee limit after their booking ended.'
             . ' BookingId: ' . $booking->getId()
        );

        $buildingId = $building->getId();
        if (isset($this->warnings[$buildingId][$user->getId()]) &&
            ! is_null($this->warnings[$buildingId][$user->getId()]['trusteesNotifiedAt'])) {
            // nothing to do
            Log::info('Trustees already warned');

            return;
        }

        // and notify the trustees
        Log::warning(
            'TACZOJ: Trustees User ' . $user->getId()
            . ' has not yet left though their booking ended at '
            . $booking->getEnd()->toDateTimeString()
        );

        $this->trusteesTeamRole->notify(new NotifyTrusteeOverstay($building, $user, $booking));

        // and remember we have sent a warning
        if (! isset($this->warnings[$buildingId][$user->getId()])) {
            // no warning for this user yet, add a new one
            $this->warnings[$buildingId][$user->getId()] = [
                'bookingId' => $booking->getId(),
                'userNotifiedAt' => null,
                'trusteesNotifiedAt' => Carbon::now(),
            ];
        } else {
            $this->warnings[$buildingId][$user->getId()]['trusteesNotifiedAt'] = Carbon::now();
        }
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
