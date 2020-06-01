<?php

namespace HMS\Gatekeeper;

use Carbon\Carbon;
use HMS\Entities\Role;
use HMS\Entities\User;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Log;
use HMS\Repositories\MetaRepository;
use HMS\Repositories\RoleRepository;
use App\Events\Gatekeeper\NewBooking;
use HMS\Entities\Gatekeeper\Building;
use HMS\User\Permissions\RoleManager;
use App\Events\Gatekeeper\BookingChanged;
use HMS\Entities\Gatekeeper\BookableArea;
use App\Events\Gatekeeper\BookingCancelled;
use HMS\Entities\Gatekeeper\BuildingAccessState;
use HMS\Entities\Gatekeeper\TemporaryAccessBooking;
use HMS\Factories\Gatekeeper\TemporaryAccessBookingFactory;
use HMS\Repositories\Gatekeeper\TemporaryAccessBookingRepository;

class TemporaryAccessBookingManager
{
    /**
     * @var TemporaryAccessBookingRepository
     */
    protected $temporaryAccessBookingRepository;

    /**
     * @var TemporaryAccessBookingFactory
     */
    protected $temporaryAccessBookingFactory;

    /**
     * @var RoleManager
     */
    protected $roleManager;

    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * @var MetaRepository
     */
    protected $metaRepository;

    /**
     * Create a new Booking Manager instance.
     *
     * @param TemporaryAccessBookingRepository $temporaryAccessBookingRepository
     * @param TemporaryAccessBookingFactory $temporaryAccessBookingFactory
     * @param RoleManager $roleManager
     * @param RoleRepository $roleRepository
     * @param MetaRepository $metaRepository
     * @param MetaRepository $metaRepository
     */
    public function __construct(
        TemporaryAccessBookingRepository $temporaryAccessBookingRepository,
        TemporaryAccessBookingFactory $temporaryAccessBookingFactory,
        RoleManager $roleManager,
        RoleRepository $roleRepository,
        MetaRepository $metaRepository
    ) {
        $this->temporaryAccessBookingRepository = $temporaryAccessBookingRepository;
        $this->temporaryAccessBookingFactory = $temporaryAccessBookingFactory;
        $this->roleManager = $roleManager;
        $this->roleRepository = $roleRepository;
        $this->metaRepository = $metaRepository;
    }

    /**
     * Make a Booking.
     *
     * @param Carbon $start
     * @param Carbon $end
     * @param User $user
     * @param BookableArea $bookableArea
     * @param string|null $notes
     * @param string|null $color
     *
     * @return string|TemporaryAccessBooking String with error message or a Booking
     */
    public function book(
        Carbon $start,
        Carbon $end,
        User $user,
        BookableArea $bookableArea,
        ?string $notes = null,
        ?string $color = null
    ) {
        $messages = collect(); // TODO: thinking of a way to pass over limit warnings back to grant.all
        $building = $bookableArea->getBuilding();

        // defaults
        $approved = false;
        $approvedBy = null;

        // BASIC CHECKS
        $maxLength = CarbonInterval::instance(
            new \DateInterval($this->metaRepository->get('temp_access_reset_interval', 'PT12H'))
        )->totalMinutes;
        $basicChecks = $this->basicTimeChecks($start, $end, $maxLength);
        if (is_string($basicChecks)) {
            return $basicChecks; // 422
        }

        // ADVANCED CHECKS

        // does it clash?
        if (! empty($this->temporaryAccessBookingRepository
            ->checkForClashByUserForBuilding($user, $building, $start, $end))
        ) {
            if ($user != \Auth::user()) {
                return 'User already has a booking in this period.'; // 409
            }

            return 'You already have a booking in this period.'; // 409
        }

        // check maxConcurrentPerUser
        $userConcurrencyCheck = $this->userConcurrencyCheck($user, $building);

        // check occupancy limits
        $occupancyChecks = $this->occupancyChecks($start, $end, $bookableArea);

        // Building access state checks
        if (\Gate::allows('gatekeeper.temporaryAccess.grant.all')) {
            // if you have the power building access state does not matter
            // the booking is automatically approved
            // building limits are ignored
            // area limits are ignored
            $approved = true;
            $approvedBy = \Auth::user();
            if (is_array($occupancyChecks)) {
                $messages = $messages->merge($occupancyChecks);
            }
            if (is_string($userConcurrencyCheck)) {
                $messages[] = $userConcurrencyCheck;
            }
        } else {
            switch ($bookableArea->getBuilding()->getAccessState()) {
                case BuildingAccessState::FULL_OPEN:
                    // what the hell are we doing here? calendar should never be seen
                    return 'Building is fully open, Booking denied.'; // 403
                case BuildingAccessState::SELF_BOOK:
                    if ($user != \Auth::user()) {
                        return 'You cannot book for someone else.';
                    }

                    if (is_string($userConcurrencyCheck)) {
                        return $userConcurrencyCheck;
                    }

                    // check bookable area limits
                    if (is_array($occupancyChecks)) {
                        // nope
                        return $occupancyChecks[0]; // 409
                    }

                    // booking can be made and is self approved
                    $approved = true;
                    $approvedBy = $user;

                    break;
                case BuildingAccessState::REQUESTED_BOOK:
                    if ($user != \Auth::user()) {
                        return 'You cannot book for someone else.';
                    }

                    if (is_string($userConcurrencyCheck)) {
                        return $userConcurrencyCheck;
                    }

                    // check bookable area limits
                    if (is_array($occupancyChecks)) {
                        return $occupancyChecks[0]; // 409
                    }
                    // booking can be made and event/listeners will take care of requesting for trustee approval
                    break;
                case BuildingAccessState::CLOSED:
                    // what the hell are we doing here? only people with grant.all should be able to book and that is checked above
                    return 'Building is closed, Booking denied.'; // 403
            }
        }

        // Phew!  We can now add the booking
        $booking = $this->temporaryAccessBookingFactory
            ->create(
                $start,
                $end,
                $user,
                $bookableArea,
                $color,
                $notes,
                $approved,
                $approvedBy
            );
        $this->temporaryAccessBookingRepository->save($booking);

        broadcast(new NewBooking($booking))->toOthers();

        return $booking;
    }

    /**
     * Update a booking.
     *
     * @param TemporaryAccessBooking $booking
     * @param Carbon|null $start
     * @param Carbon|null $end
     *
     * @return string|TemporaryAccessBooking String with error message or a Booking
     */
    public function update(TemporaryAccessBooking $booking, Carbon $start = null, Carbon $end = null)
    {
        if (is_null($start)) {
            // not changing the start
            $start = $booking->getStart();
        }

        if (is_null($end)) {
            // not chaning the end
            $end = $booking->getEnd();
        }

        $maxLength = CarbonInterval::instance(
            new \DateInterval($this->metaRepository->get('temp_access_reset_interval', 'PT12H'))
        )->totalMinutes;
        $basicChecks = $this->basicTimeChecks($start, $end, $maxLength);

        // Hack around time checks for a booking that is 'now'
        if ($start == $booking->getStart() && $basicChecks == 'Start date cannot be in the past') {
            $basicChecks = true;
        }

        if (is_string($basicChecks)) {
            return $basicChecks; // 422
        }

        $orignalBooking = $booking;

        // all check passed lets update it
        $booking->setStart($start);
        $booking->setEnd($end);
        $this->temporaryAccessBookingRepository->save($booking);

        broadcast(new BookingChanged($orignalBooking, $booking))->toOthers();

        return $booking;
    }

    /**
     * Cancel a Booking.
     *
     * @param TemporaryAccessBooking $booking
     *
     * @return array|string
     */
    public function cancel(TemporaryAccessBooking $booking)
    {
        // grab the Id before we remove the event
        $bookingId = $booking->getId();
        $buildingId = $booking->getBookableArea()->getBuilding()->getId();
        $this->temporaryAccessBookingRepository->remove($booking);

        broadcast(new BookingCancelled($bookingId, $buildingId))->toOthers();

        return [
            'bookingId' => $bookingId,
        ];
    }

    /**
     * Do some basic time checks. Cancelled.
     *
     * @param Carbon $start
     * @param Carbon $end
     * @param int $maxLength
     *
     * @return bool|string String with error message or true if al checks passed
     */
    protected function basicTimeChecks(Carbon $start, Carbon $end, int $maxLength)
    {
        // check start date is in the future (within 15 minutes)
        $now = Carbon::now('Europe/London')->subMinutes(15);
        if ($start <= $now) {
            return 'Start date cannot be in the past'; // 422
        }

        // check the end date is in the future (within 30 minutes)
        if ($end <= $now) {
            return 'End date cannot be in the past'; // 422
        }

        // check the end date is after the start date
        if ($end < $start) {
            return 'End date must be after the start date'; // 422
        }

        // check the end and start date aren't the same?
        if ($end == $start) {
            return 'Length of booking must be greater than zero!'; // 422
        }

        // check length <= max
        $length = $end->diffInMinutes($start);
        if ($length > $maxLength) {
            return 'Maximum booking time is ' . $maxLength . ' minutes for this tool'; //422
        }

        return true;
    }

    /**
     * Check user concurrent booking limit.
     *
     * @param User     $user
     * @param Building $building
     *
     * @return bool|string String with error message or true if al checks passed
     */
    protected function userConcurrencyCheck(User $user, Building $building)
    {
        // TODO: should this be per building or over all buildings (if so we wont need $building)
        $maxConcurrentPerUser = $this->getSelfBookSettings()['maxConcurrentPerUser'];

        $futureCount = $this->temporaryAccessBookingRepository
            ->countFutureByBuildingAndUser($building, $user);

        if ($futureCount >= $maxConcurrentPerUser) {
            $txt = $maxConcurrentPerUser > 1 ? 'bookings' : 'booking';

            if ($user != \Auth::user()) {
                return 'Maximum current/future ' . $txt . ' of ' . $maxConcurrentPerUser . ' exceed for User.'; // 409 ?
            }

            return 'You can only have ' . $maxConcurrentPerUser . ' current/future ' . $txt . '.'; // 409 ?
        }

        return true;
    }

    /**
     * Check if this booking would violate the any occupancy limits.
     *
     * @param Carbon   $start
     * @param Carbon   $end
     * @param BookableArea $bookableArea
     * @param int $guests TODO
     * @param TemporaryAccessBooking $booking
     *
     * @return bool|array array of error messages or true if all checks passed
     */
    protected function occupancyChecks(
        Carbon $start,
        Carbon $end,
        BookableArea $bookableArea,
        // int $guests = 0,
        TemporaryAccessBooking $booking = null
    ) {
        $messages = [];
        $building = $bookableArea->getBuilding();

        $bookings = collect($this->temporaryAccessBookingRepository->findBetweenForBuilding($start, $end, $building));

        // need to filter out the current booking, if we where given one
        $bookings = $bookings->filter(function ($item) use ($booking) {
            return $item != $booking;
        });

        // TODO: guests
        if ($bookings->count() >= $building->getSelfBookMaxOccupancy()) {
            $messages[] = 'Building maximum concurrent occupancy reached.';
        }

        // now filter bookings by the area
        $bookingsForArea = $bookings->filter(function ($item) use ($bookableArea) {
            return $item->getBookableArea() == $bookableArea;
        });

        // TODO: guests
        if ($bookingsForArea->count() >= $bookableArea->getMaxOccupancy()) {
            $messages[] = $bookableArea->getName() . ' area maximum concurrent occupancy reached.';
        }

        return count($messages) ? $messages : true;
    }

    /**
     * Update user.temporaryAccess role for User that are currently booked.
     */
    public function updateTemporaryAccessRole()
    {
        $temporaryAccessRole = $this->roleRepository->findOneByName(Role::TEMPORARY_ACCESS);
        $currentTemporaryAccessBookings = $this->temporaryAccessBookingRepository->findBetween(
            Carbon::now()->subMinutes(10),
            Carbon::now()->addMinutes(10)
        );

        $currentTemporaryAccessUsers = array_map(function ($tba) {
            return $tba->getUser();
        }, $currentTemporaryAccessBookings);

        // remove any users that are not currently booked
        $resetUserCount = 0;
        foreach ($temporaryAccessRole->getUsers() as $user) {
            if (! in_array($user, $currentTemporaryAccessUsers)) {
                $this->roleManager->removeUserFromRole($user, $temporaryAccessRole);
                $resetUserCount++;
            }
        }

        if ($resetUserCount) {
            Log::info(
                'TemporaryAccessBookingManager@updateTemporaryAccessRole: Removed temporary access for '
                . $resetUserCount . ' users.'
            );
        }

        // add role to any user that done currently have it
        $addUserCount = 0;
        foreach ($currentTemporaryAccessUsers as $user) {
            if (! $user->hasRole($temporaryAccessRole)) {
                $this->roleManager->addUserToRole($user, $temporaryAccessRole);
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

    /**
     * Remove all future Bookings for a buildings BookableAreas.
     *
     * @param Building $building
     */
    public function removeAllFutureBookingsForBuilding(Building $building)
    {
        //
    }

    /**
     * Unapprove future Bookings for a Buildings BookableAreas unless the Booking User can gatekeeper.access.manage.
     *
     * @param Building $building
     */
    public function unapproveFutureBookingsForBuildingUnlessManger(Building $building)
    {
        //
    }

    /**
     * Remove future Bookings for a Buildings BookableAreas unless the Booking User can gatekeeper.access.manage.
     *
     * @param Building $building
     */
    public function removeFutureBookingsForBuildingUnlessManager(Building $building)
    {
        //
    }

    /**
     * Get all the self book setting From Meta.
     *
     * @return array
     */
    public function getSelfBookSettings()
    {
        return [
            'maxLength' => $this->metaRepository->getInt('self_book_max_length'),
            'maxConcurrentPerUser' => $this->metaRepository->getInt('self_book_max_concurrent_per_user'),
            'maxGuestsPerUser' => $this->metaRepository->getInt('self_book_max_guests_per_user'),
            'minPeriodBetweenBookings' => $this->metaRepository->getInt('self_book_min_period_between_bookings'),
            'bookingInfoText' => $this->metaRepository->get('self_book_info_text'),
        ];
    }

    /**
     * Get all settings needed by TemporaryAccess.vue calendar.
     *
     * @return array
     */
    public function getTemporaryAccessSettings()
    {
        // base self book globals
        $settings = $this->getSelfBookSettings();

        // and this users settings
        $settings['userId'] = \Auth::user()->getId();
        // so we can de-anonymize a broadcast booking
        $settings['fullname'] = \Auth::user()->getFullname();

        if (\Gate::allows('gatekeeper.temporaryAccess.grant.all')) {
            $settings['grant'] = 'ALL';
        } elseif (\Gate::allows('gatekeeper.temporaryAccess.grant.self')) {
            $settings['grant'] = 'SELF';
        } else {
            $settings['grant'] = 'NONE';
        }

        $settings['userCurrentCountByBuildingId'] = $this->temporaryAccessBookingRepository
                ->countFutureForUserByBuildings(\Auth::user());

        return $settings;
    }
}
