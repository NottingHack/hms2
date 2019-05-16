<?php

namespace HMS\Tools;

use Carbon\Carbon;
use HMS\Entities\User;
use HMS\Entities\Tools\Tool;
use HMS\Entities\Tools\Booking;
use App\Events\Tools\NewBooking;
use HMS\Entities\Tools\BookingType;
use App\Events\Tools\BookingChanged;
use App\Events\Tools\BookingCancelled;
use HMS\Factories\Tools\BookingFactory;
use HMS\Repositories\Tools\BookingRepository;

class BookingManager
{
    /**
     * @var BookingRepository
     */
    protected $bookingRepository;

    /**
     * @var BookingFactory
     */
    protected $bookingFactory;

    /**
     * Create a new Booking Manager instance.
     *
     * @param BookingRepository $bookingRepository
     * @param BookingFactory $bookingFactory
     */
    public function __construct(
        BookingRepository $bookingRepository,
        BookingFactory $bookingFactory
    ) {
        $this->bookingRepository = $bookingRepository;
        $this->bookingFactory = $bookingFactory;
    }

    /**
     * Make a normal tool Booking.
     *
     * @param Tool $tool
     * @param Carbon $start
     * @param Carbon $end
     * @param User|null $user
     *
     * @return string|Booking String with error message or a Booking
     */
    public function bookNormal(Tool $tool, Carbon $start, Carbon $end, User $user = null)
    {
        if (is_null($user)) {
            $user = \Auth::user();
        }

        // BASIC CHECKS
        // can this user post this event?
        // Is the tool restricted and has the user been inducted
        if ($tool->isRestricted() && $user->cannot('tools.' . $tool->getPermissionName() . '.book')) {
            return 'Must be inducted to book this tool.'; // 403
        }

        $basicChecks = $this->basicTimeChecks($start, $end, $tool->getLengthMax());
        if (is_string($basicChecks)) {
            return $basicChecks; // 422
        }

        // ADVANCED CHECKS
        // how many existing bookings does user have?
        if ($this->bookingRepository->countNormalByToolAndUser($tool, $user) >= $tool->getBookingsMax()) {
            $txt = $tool->getBookingsMax() > 1 ? 'bookings' : 'booking';

            return 'You can only have ' . $tool->getBookingsMax() . ' ' . $txt . ' for this tool.'; // 409 ?
        }

        // does it clash?
        if (! empty($this->bookingRepository->checkForClashByTool($tool, $start, $end))) {
            return 'Your booking request clashes with another booking. No booking has been made.'; // 409
        }

        // Phew!  We can now add the booking
        $booking = $this->bookingFactory->create($start, $end, BookingType::NORMAL, $user, $tool);
        $this->bookingRepository->save($booking);

        event(new NewBooking($booking));

        return $booking;
    }

    /**
     * Make a Induction tool Booking.
     *
     * @param Tool $tool
     * @param Carbon $start
     * @param Carbon $end
     * @param User|null $user
     *
     * @return string|Booking String with error message or a Booking
     */
    public function bookInduction(Tool $tool, Carbon $start, Carbon $end, User $user = null)
    {
        if (is_null($user)) {
            $user = \Auth::user();
        }

        // BASIC CHECKS
        // can this user post this event?
        // Is the tool restricted and has the user been inducted
        if ($tool->isRestricted() && $user->cannot('tools.' . $tool->getPermissionName() . '.book.induction')) {
            return 'Must be inducted to book this tool.';
        }

        $basicChecks = $this->basicTimeChecks($start, $end, $tool->getLengthMax());
        if (is_string($basicChecks)) {
            return $basicChecks;
        }

        // ADVANCED CHECKS
        // does it clash?
        if (! empty($this->bookingRepository->checkForClashByTool($tool, $start, $end))) {
            return 'Your booking request clashes with another booking. No booking has been made.';
        }

        // Phew!  We can now add the booking
        $booking = $this->bookingFactory->create($start, $end, BookingType::INDUCTION, $user, $tool);
        $this->bookingRepository->save($booking);

        event(new NewBooking($booking));

        return $booking;
    }

    /**
     * Make a Maintenance tool Booking.
     *
     * @param Tool $tool
     * @param Carbon $start
     * @param Carbon $end
     * @param User|null $user
     *
     * @return string|Booking String with error message or a Booking
     */
    public function bookMaintenance(Tool $tool, Carbon $start, Carbon $end, User $user = null)
    {
        if (is_null($user)) {
            $user = \Auth::user();
        }

        // BASIC CHECKS
        // can this user post this event?
        // Is the tool restricted and has the user been inducted
        if ($user->cannot('tools.' . $tool->getPermissionName() . '.book.maintenance')) {
            return 'Must be inducted to book this tool.';
        }

        // Maintenance slot length can be to the end of the day
        $maxLength = $start->diffInMinutes($start->copy()->endOfDay());

        $basicChecks = $this->basicTimeChecks($start, $end, $maxLength);
        if (is_string($basicChecks)) {
            return $basicChecks;
        }

        // ADVANCED CHECKS
        // does it clash?
        // TODO: Maintenance slot can overwrite other bookings
        if (! empty($this->bookingRepository->checkForClashByTool($tool, $start, $end))) {
            return 'Your booking request clashes with another booking. No booking has been made.';
        }

        // Phew!  We can now add the booking
        $booking = $this->bookingFactory->create($start, $end, BookingType::MAINTENANCE, $user, $tool);
        $this->bookingRepository->save($booking);

        event(new NewBooking($booking));

        return $booking;
    }

    /**
     * Update a booking.
     *
     * @param Tool $tool
     * @param Booking $booking
     * @param Carbon|null $start
     * @param Carbon|null $end
     *
     * @return string|Booking String with error message or a Booking
     */
    public function update(Tool $tool, Booking $booking, Carbon $start = null, Carbon $end = null)
    {
        $user = \Auth::user();

        if ($user->getId() != $booking->getUser()->getId()) {
            return 'This is not your booking.'; // 403
        }

        if ($tool->getId() != $booking->getTool()->getId()) {
            return 'This booking is not for this tool.'; // 422
        }

        if (is_null($start)) {
            // not changing the start
            $start = $booking->getStart();
        }

        if (is_null($end)) {
            // not chaning the end
            $end = $booking->getEnd();
        }

        if ($booking->getType() == BookingType::MAINTENANCE) {
            // Maintenance slot length can be to the end of the day
            $maxLength = $start->diffInMinutes($start->copy()->endOfDay());
        } else {
            $maxLength = $tool->getLengthMax();
        }

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
        $this->bookingRepository->save($booking);
        event(new BookingChanged($orignalBooking, $booking));

        return $booking;
    }

    /**
     * Cancel a Booking.
     *
     * @param Booking $booking
     *
     * @return bool|string
     */
    public function cancel(Booking $booking)
    {
        $user = \Auth::user();

        if ($user->getId() != $booking->getUser()->getId()) {
            return 'This is not your booking.'; // 403
        }

        // grab the Id before we remove the event
        $tool = $booking->getTool();
        $bookingId = $booking->getId();
        $this->bookingRepository->remove($booking);

        event(new BookingCancelled($tool, $bookingId));

        return true;
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
}
