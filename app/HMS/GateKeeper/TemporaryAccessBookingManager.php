<?php

namespace HMS\GateKeeper;

use Carbon\Carbon;
use HMS\Entities\Role;
use HMS\Entities\User;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Log;
use HMS\Repositories\MetaRepository;
use HMS\Repositories\RoleRepository;
use App\Events\Gatekeeper\NewBooking;
use HMS\User\Permissions\RoleManager;
use App\Events\Gatekeeper\BookingChanged;
use App\Events\Gatekeeper\BookingCancelled;
use HMS\Entities\GateKeeper\TemporaryAccessBooking;
use HMS\Factories\GateKeeper\TemporaryAccessBookingFactory;
use HMS\Repositories\GateKeeper\TemporaryAccessBookingRepository;

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
     *
     * @return string|TemporaryAccessBooking String with error message or a Booking
     */
    public function book(Carbon $start, Carbon $end, User $user)
    {
        // BASIC CHECKS
        $maxLength = CarbonInterval::instance(new \DateInterval($this->metaRepository->get('temp_access_reset_interval', 'PT12H')))->totalMinutes;
        $basicChecks = $this->basicTimeChecks($start, $end, $maxLength);
        if (is_string($basicChecks)) {
            return $basicChecks; // 422
        }

        // ADVANCED CHECKS

        // does it clash?
        if (! empty($this->temporaryAccessBookingRepository->checkForClashByUser($user, $start, $end))) {
            return 'Your booking request clashes with another booking. No booking has been made.'; // 409
        }

        // Phew!  We can now add the booking
        $booking = $this->temporaryAccessBookingFactory->create($start, $end, $user);
        $this->temporaryAccessBookingRepository->save($booking);

        event(new NewBooking($booking));

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

        $maxLength = CarbonInterval::instance(new \DateInterval($this->metaRepository->get('temp_access_reset_interval', 'PT12H')))->totalMinutes;
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
        event(new BookingChanged($orignalBooking, $booking));

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
        $this->temporaryAccessBookingRepository->remove($booking);

        event(new BookingCancelled($bookingId));

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
     * @return bool|string
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
     * Update user.temporaryAccess role for User that are currently booked.
     */
    public function updateTemporaryAccessRole()
    {
        $temporaryAccessRole = $this->roleRepository->findOneByName(Role::TEMPORARY_ACCESS);
        $currentTemporaryAccessBookings = $this->temporaryAccessBookingRepository->findBetween(Carbon::now()->subMinutes(10), Carbon::now()->addMinutes(10));

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
            Log::info('TemporaryAccessBookingManager@updateTemporaryAccessRole: Removed temporary access for ' . $resetUserCount . ' users.');
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
            Log::info('TemporaryAccessBookingManager@updateTemporaryAccessRole: Added temporary access for ' . $addUserCount . ' users.');
        }
    }
}
