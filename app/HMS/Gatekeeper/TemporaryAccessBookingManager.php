<?php

namespace HMS\Gatekeeper;

use Carbon\Carbon;
use HMS\Entities\User;
use Carbon\CarbonInterval;
use HMS\Repositories\MetaRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Events\Gatekeeper\NewBooking;
use HMS\Entities\Gatekeeper\Building;
use App\Events\Gatekeeper\BookingChanged;
use HMS\Entities\Gatekeeper\BookableArea;
use App\Events\Gatekeeper\BookingApproved;
use App\Events\Gatekeeper\BookingRejected;
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
     * @var MetaRepository
     */
    protected $metaRepository;

    /**
     * Create a new Booking Manager instance.
     *
     * @param TemporaryAccessBookingRepository $temporaryAccessBookingRepository
     * @param TemporaryAccessBookingFactory $temporaryAccessBookingFactory
     * @param MetaRepository $metaRepository
     */
    public function __construct(
        TemporaryAccessBookingRepository $temporaryAccessBookingRepository,
        TemporaryAccessBookingFactory $temporaryAccessBookingFactory,
        MetaRepository $metaRepository
    ) {
        $this->temporaryAccessBookingRepository = $temporaryAccessBookingRepository;
        $this->temporaryAccessBookingFactory = $temporaryAccessBookingFactory;
        $this->metaRepository = $metaRepository;
    }

    /**
     * Make a Booking.
     *
     * @param Carbon $start
     * @param Carbon $end
     * @param User $user
     * @param BookableArea $bookableArea
     * @param int $guests
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
        int $guests = 0,
        ?string $notes = null,
        ?string $color = null
    ) {
        $messages = collect(); // TODO: thinking of a way to pass over limit warnings back to grant.all

        // check bookableArea isSelfBookable
        if (Gate::denies('gatekeeper.temporaryAccess.grant.all') && ! $bookableArea->isSelfBookable()) {
            return 'You can not book the area.';
        }

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

        // A user may not have overlapping bookings.
        $overlappingPerUserCheck = $this->overlappingPerUserCheck($start, $end, $user, $building);
        if (is_string($overlappingPerUserCheck)) {
            return $overlappingPerUserCheck;
        }

        // A user may only have {{ maxConcurrentPerUser }} current and future bookings at one time.
        $concurrentPerUserCheck = $this->concurrentPerUserCheck($user, $building);

        // A user muse have {{ minPeriodBetweenBookings }} gap between bookings.
        $minPeriodBetweenBookingsPerUserCheck = $this->minPeriodBetweenBookingsPerUserCheck($start, $end, $user, $building);

        // check occupancy limits
        $occupancyChecks = $this->occupancyChecks($start, $end, $bookableArea, $guests);

        // Building access state checks
        if (Gate::allows('gatekeeper.temporaryAccess.grant.all')) {
            // if you have the power building access state does not matter
            // the booking is automatically approved
            // building limits are ignored
            // area limits are ignored
            $approved = true;
            $approvedBy = Auth::user();
            if (is_array($occupancyChecks)) {
                $messages = $messages->merge($occupancyChecks);
            }

            if (is_string($concurrentPerUserCheck)) {
                $messages[] = $concurrentPerUserCheck;
            }

            if (is_string($minPeriodBetweenBookingsPerUserCheck)) {
                $messages[] = $minPeriodBetweenBookingsPerUserCheck;
            }
        } else {
            switch ($bookableArea->getBuilding()->getAccessState()) {
                case BuildingAccessState::FULL_OPEN:
                    // what the hell are we doing here? calendar should never be seen
                    return 'Building is fully open, Booking denied.'; // 403
                case BuildingAccessState::SELF_BOOK:
                    if ($user != Auth::user()) {
                        return 'You cannot book for someone else.';
                    }

                    if (is_string($concurrentPerUserCheck)) {
                        return $concurrentPerUserCheck;
                    }

                    if (is_string($minPeriodBetweenBookingsPerUserCheck)) {
                        return $minPeriodBetweenBookingsPerUserCheck;
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
                    if ($user != Auth::user()) {
                        return 'You cannot book for someone else.';
                    }

                    if (is_string($concurrentPerUserCheck)) {
                        return $concurrentPerUserCheck;
                    }

                    if (is_string($minPeriodBetweenBookingsPerUserCheck)) {
                        return $minPeriodBetweenBookingsPerUserCheck;
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

        // Phew! We can now add the booking
        $booking = $this->temporaryAccessBookingFactory
            ->create(
                $start,
                $end,
                $user,
                $bookableArea,
                $guests,
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
        $messages = collect(); // TODO: thinking of a way to pass over limit warnings back to grant.all
        // check isApproved
        if (Gate::denies('gatekeeper.temporaryAccess.grant.all')
            && $booking->isApproved() && $booking->getApprovedBy() != Auth::user()) {
            return 'You can not change an approved booking.';
        }

        if (is_null($start)) {
            // not changing the start
            $start = $booking->getStart();
        }

        if (is_null($end)) {
            // not changing the end
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

        // TODO: ADVANCED CHECKS
        $user = $booking->getUser();
        $guests = $booking->getGuests();
        $bookableArea = $booking->getBookableArea();
        $building = $bookableArea->getBuilding();

        // A user may not have overlapping bookings.
        $overlappingPerUserCheck = $this->overlappingPerUserCheck($start, $end, $user, $building, $booking);
        if (is_string($overlappingPerUserCheck)) {
            return $overlappingPerUserCheck;
        }

        // A user may only have {{ maxConcurrentPerUser }} current and future bookings at one time.
        // TODO: this may not be relevant for an update
        $concurrentPerUserCheck = $this->concurrentPerUserCheck($user, $building, $booking);

        // A user muse have {{ minPeriodBetweenBookings }} gap between bookings.
        $minPeriodBetweenBookingsPerUserCheck = $this->minPeriodBetweenBookingsPerUserCheck($start, $end, $user, $building, $booking);

        // check occupancy limits
        $occupancyChecks = $this->occupancyChecks(
            $start,
            $end,
            $bookableArea,
            $guests,
            $booking
        );

        // Building access state checks
        if (Gate::allows('gatekeeper.temporaryAccess.grant.all')) {
            // if you have the power building access state does not matter
            // the booking is automatically approved
            // building limits are ignored
            // area limits are ignored
            if (is_array($occupancyChecks)) {
                $messages = $messages->merge($occupancyChecks);
            }

            if (is_string($concurrentPerUserCheck)) {
                $messages[] = $concurrentPerUserCheck;
            }

            if (is_string($minPeriodBetweenBookingsPerUserCheck)) {
                $messages[] = $minPeriodBetweenBookingsPerUserCheck;
            }
        } else {
            switch ($bookableArea->getBuilding()->getAccessState()) {
                case BuildingAccessState::FULL_OPEN:
                    // what the hell are we doing here? calendar should never be seen
                    return 'Building is fully open, Booking update denied.'; // 403
                case BuildingAccessState::SELF_BOOK:
                    if ($user != Auth::user()) {
                        return "You cannot update someone else's booking.";
                    }

                    if (is_string($concurrentPerUserCheck)) {
                        return $concurrentPerUserCheck;
                    }

                    if (is_string($minPeriodBetweenBookingsPerUserCheck)) {
                        return $minPeriodBetweenBookingsPerUserCheck;
                    }

                    // check bookable area limits
                    if (is_array($occupancyChecks)) {
                        // nope
                        return $occupancyChecks[0]; // 409
                    }

                    break;
                case BuildingAccessState::REQUESTED_BOOK:
                    if ($user != Auth::user()) {
                        return "You cannot update someone else's booking.";
                    }

                    if (is_string($concurrentPerUserCheck)) {
                        return $concurrentPerUserCheck;
                    }

                    if (is_string($minPeriodBetweenBookingsPerUserCheck)) {
                        return $minPeriodBetweenBookingsPerUserCheck;
                    }

                    // check bookable area limits
                    if (is_array($occupancyChecks)) {
                        return $occupancyChecks[0]; // 409
                    }
                    // booking can be made and event/listeners will take care of requesting for trustee approval
                    break;
                case BuildingAccessState::CLOSED:
                    // what the hell are we doing here? only people with grant.all should be able to book and that is checked above
                    return 'Building is closed, update denied.'; // 403
            }
        }

        // Phew! All check passed lets update it
        $orignalBooking = $booking; // save original < this may not do what i think!!!
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

        // TODO: return what is now lastestBooking
        return [
            'bookingId' => $bookingId,
        ];
    }

    /**
     * Approve this booking request.
     *
     * @param TemporaryAccessBooking $booking
     * @param bool $approve
     *
     * @return string|TemporaryAccessBooking String with error message or a Booking
     */
    public function approve(TemporaryAccessBooking $booking, bool $approve)
    {
        // checks?

        // ok approve it
        $booking->setApproved(true);
        $booking->setApprovedBy(Auth::user());
        $this->temporaryAccessBookingRepository->save($booking);

        // fire event, this should update calendar views and send email
        broadcast(new BookingApproved($booking))->toOthers();

        return $booking;
    }

    /**
     * Reject this booking request.
     *
     * @param TemporaryAccessBooking $booking
     * @param string                 $reason
     *
     * @return string|TemporaryAccessBooking String with error message or a Booking
     */
    public function reject(TemporaryAccessBooking $booking, string $reason)
    {
        // checks?

        // grab id for response
        $bookingId = $booking->getId();
        // fire event, this should update calendar views and send email
        // so long as everything is queued the the booking should be serialised so we can still get its data
        broadcast(new BookingRejected($booking, $reason, Auth::user()));

        // actually remove the booking once the event has serialized it
        $this->temporaryAccessBookingRepository->remove($booking);

        return [
            'bookingId' => $bookingId,
            'message' => 'Booking rejected and member notified',
        ];
    }

    /**
     * Cancel a previously approved booking.
     * TODO: yeah I'm reusing the reject code, lwk.
     * NotifyBookingRejected takes care of sending a different email.
     *
     * @param TemporaryAccessBooking $booking
     * @param string                 $reason
     *
     * @return string|TemporaryAccessBooking String with error message or a Booking
     */
    public function cancelWithReason(TemporaryAccessBooking $booking, string $reason)
    {
        // checks?

        // grab id for response
        $bookingId = $booking->getId();
        // fire event, this should update calendar views and send email
        // so long as everything is queued the the booking should be serialised so we can still get its data
        broadcast(new BookingRejected($booking, $reason, Auth::user()));

        // actually remove the booking once the event has serialized it
        $this->temporaryAccessBookingRepository->remove($booking);

        return [
            'bookingId' => $bookingId,
            'message' => 'Booking rejected and member notified',
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
     * A user may not have overlapping bookings.
     *
     * @param Carbon   $start
     * @param Carbon   $end
     * @param User                        $user
     * @param Building                    $building
     * @param TemporaryAccessBooking|null $ignoreBooking
     *
     * @return bool|string String with error message or true if al checks passed
     */
    public function overlappingPerUserCheck(
        Carbon $start,
        Carbon $end,
        User $user,
        Building $building,
        TemporaryAccessBooking $ignoreBooking = null
    ) {
        // look for any bookings this user might already have in this time period
        $bookings = collect($this->temporaryAccessBookingRepository
                    ->findBetweenForBuildingAndUser($start, $end, $building, $user));

        // need to filter out the current booking, if we where given one
        $bookings = $bookings->reject(function ($item) use ($ignoreBooking) {
            return $item == $ignoreBooking;
        });

        if ($bookings->isNotEmpty()) {
            if ($user != Auth::user()) {
                return 'User already has a booking in this period.'; // 409
            }

            return 'You already have a booking in this period.'; // 409
        }

        return true;
    }

    /**
     * A user may only have {{ maxConcurrentPerUser }} current and future bookings at one time.
     *
     * @param User     $user
     * @param Building $building
     * @param TemporaryAccessBooking|null $ignoreBooking
     *
     * @return bool|string String with error message or true if al checks passed
     */
    protected function concurrentPerUserCheck(
        User $user,
        Building $building,
        TemporaryAccessBooking $ignoreBooking = null
    ) {
        // TODO: should this be per building or over all buildings (if so we wont need $building)
        $maxConcurrentPerUser = $this->getSelfBookSettings()['maxConcurrentPerUser'];

        $futureCount = $this->temporaryAccessBookingRepository
            ->countFutureForBuildingAndUser($building, $user, $ignoreBooking);

        if ($futureCount >= $maxConcurrentPerUser) {
            $b = $maxConcurrentPerUser > 1 ? 'bookings' : 'booking';

            if ($user != Auth::user()) {
                return 'Maximum current/future ' . $b . ' of ' . $maxConcurrentPerUser . ' exceed for User.'; // 409 ?
            }

            return 'You can only have ' . $maxConcurrentPerUser . ' current or future ' . $b . '.'; // 409 ?
        }

        return true;
    }

    /**
     * A user muse have {{ minPeriodBetweenBookings }} gap between bookings.
     *
     * @param Carbon   $start
     * @param Carbon   $end
     * @param User     $user
     * @param Building $building
     * @param TemporaryAccessBooking|null $ignoreBooking
     *
     * @return bool|string String with error message or true if al checks passed
     */
    protected function minPeriodBetweenBookingsPerUserCheck(
        Carbon $start,
        Carbon $end,
        User $user,
        Building $building,
        TemporaryAccessBooking $ignoreBooking = null
    ) {
        $minPeriodBetweenBookings = $this->getSelfBookSettings()['minPeriodBetweenBookings'];

        $startWindow = $start->copy()->subMinutes($minPeriodBetweenBookings);
        $endWindow = $end->copy()->addMinutes($minPeriodBetweenBookings);

        // look for any bookings this user might already have in this time period
        $bookings = collect($this->temporaryAccessBookingRepository
                    ->findBetweenForBuildingAndUser($startWindow, $endWindow, $building, $user));

        // need to filter out the current booking, if we where given one
        $bookings = $bookings->reject(function ($item) use ($ignoreBooking) {
            return $item == $ignoreBooking;
        });

        if ($bookings->isNotEmpty()) {
            if ($user != Auth::user()) {
                return 'User already has a booking within ' . $minPeriodBetweenBookings . ' minutes of this slot.'; // 409
            }

            return 'You already have a booking within ' . $minPeriodBetweenBookings . ' minutes of this slot.'; // 409
        }

        return true;
    }

    /**
     * Check if this booking would violate the any occupancy limits.
     *
     * @param Carbon   $start
     * @param Carbon   $end
     * @param BookableArea $bookableArea
     * @param int $guests                               number of guests we are trying to book for or in update the number of guest from ignoreBooking
     * @param TemporaryAccessBooking $ignoreBooking     ignore this booking when calculating limits
     *
     * @return bool|array array of error messages or true if all checks passed
     */
    protected function occupancyChecks(
        Carbon $start,
        Carbon $end,
        BookableArea $bookableArea,
        int $guests = 0,
        TemporaryAccessBooking $ignoreBooking = null
    ) {
        $messages = [];
        $building = $bookableArea->getBuilding();

        $bookings = collect($this->temporaryAccessBookingRepository->findBetweenForBuilding($start, $end, $building));

        // need to filter out the current booking, if we where given one to ignore
        $bookings = $bookings->reject(function ($item) use ($ignoreBooking) {
            return $item == $ignoreBooking;
        });

        $loopStart = $start->clone();
        $fiftenMinutes = 15; // minutes

        do {
            $filterEnd = $loopStart->clone()->addMinutes($fiftenMinutes);

            $filteredBookings = $bookings->filter(function ($booking) use ($filterEnd, $loopStart) {
                return $booking->getStart()->isBefore($filterEnd) && $booking->getEnd()->isAfter($loopStart);
            });

            // check building limit
            $filteredBookingsGuests = $filteredBookings->sum->getGuests();
            $occupancyCount = $filteredBookings->count() + $filteredBookingsGuests;

            // if (current bookings inc guest) + our booking + our guests  is greater that the building getSelfBookMaxOccupancy
            if ($occupancyCount + 1 + $guests > $building->getSelfBookMaxOccupancy()) {
                $messages[] = 'Maximum building concurrent occupancy limit is '
                    . $building->getSelfBookMaxOccupancy() . '.';
                break;
            }

            // check bookable area limit

            // ok now we only want bookings for this area
            $filteredBookings = $filteredBookings->filter(function ($booking) use ($bookableArea) {
                return $booking->getBookableArea()->getId() == $bookableArea->getId();
            });

            $filteredBookingsGuests = $filteredBookings->sum->getGuests();
            $occupancyCount = $filteredBookings->count() + $filteredBookingsGuests;

            if ($bookableArea->getMaxOccupancy() == 1 && $bookableArea->getAdditionalGuestOccupancy() != 0) {
                if ($occupancyCount != 0 || $guests > $bookableArea->getAdditionalGuestOccupancy()) {
                    $messages[] = 'Area maximum concurrent occupancy limit is '
                        . $bookableArea->getMaxOccupancy() . '.';
                    break;
                }
                // allowed, me + number of guest is less than me + additionalGuestOccupancy
            } elseif ($occupancyCount + 1 + $guests > $bookableArea->getMaxOccupancy()) { // check filteredEvents counts + booking + guests vs maxOccupancy
                $messages[] = 'Area maximum concurrent occupancy limit is '
                    . $bookableArea->getMaxOccupancy() . '.';
                break;
            }

            // adjust start for next loop
            $loopStart->addMinutes($fiftenMinutes);
        } while ($loopStart->isBefore($end));

        return count($messages) ? $messages : true;
    }

    /**
     * Remove all future Bookings for a buildings BookableAreas.
     *
     * @param Building $building
     */
    public function removeAllFutureBookingsForBuilding(Building $building)
    {
        // job from BuildingManager
    }

    /**
     * Un-approve future Bookings for a Buildings BookableAreas unless the Booking User can gatekeeper.access.manage.
     *
     * @param Building $building
     */
    public function unapproveFutureBookingsForBuildingUnlessManger(Building $building)
    {
        // job from BuildingManager
    }

    /**
     * Remove future Bookings for a Buildings BookableAreas unless the Booking User can gatekeeper.access.manage.
     *
     * @param Building $building
     */
    public function removeFutureBookingsForBuildingUnlessManager(Building $building)
    {
        // job from BuildingManager
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

        $user = Auth::user();

        // and this users settings
        $settings['userId'] = $user->getId();
        // so we can de-anonymize a broadcast booking
        $settings['fullname'] = $user->getFullname();

        // there access level
        if ($user->can('gatekeeper.temporaryAccess.grant.all')) {
            $settings['grant'] = 'ALL';
        } elseif ($user->can('gatekeeper.temporaryAccess.grant.self')) {
            $settings['grant'] = 'SELF';
        } else {
            $settings['grant'] = 'NONE';
        }

        if ($user->can('gatekeeper.temporaryAccess.view.all')) {
            $settings['view'] = 'ALL';
        } elseif ($user->can('gatekeeper.temporaryAccess.view.self')) {
            $settings['view'] = 'SELF';
        } else {
            // should not even be rendering a calendar
            $settings['view'] = 'NONE';
        }

        // for minPeriodBetweenBookings Latest
        $userLatestBookins = $this->temporaryAccessBookingRepository
            ->latestBookingForUserByBuildings($user);

        // for maxConcurrentPerUser
        $userCurrentCount = $this->temporaryAccessBookingRepository
                ->countFutureForUserByBuildings($user);

        // ddd($userLatestBookins, $userCurrentCount);

        $settings['userByBuildingId'] = collect($userCurrentCount)
            ->map(function ($item, $key) use ($userLatestBookins) {
                return [
                    'futureCount' => $item,
                    'latestBookingId' => isset($userLatestBookins[$key]) ? $userLatestBookins[$key]->getId() : null,
                    'latestBookingEnd' => isset($userLatestBookins[$key]) ? $userLatestBookins[$key]->getEnd() : null,
                ];
            })
            ->all();

        return $settings;
    }
}
