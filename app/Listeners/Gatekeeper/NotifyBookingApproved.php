<?php

namespace App\Listeners\Gatekeeper;

use App\Events\Gatekeeper\BookingApproved;
use App\Notifications\Gatekeeper\BookingApproved as BookingApprovedNotification;
use Carbon\Carbon;
use HMS\Facades\Meta;
use HMS\Repositories\Gatekeeper\TemporaryAccessBookingRepository;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyBookingApproved implements ShouldQueue
{
    /**
     * @var TemporaryAccessBookingRepository
     */
    protected $temporaryAccessBookingRepository;

    /**
     * Create the event listener.
     *
     * @param TemporaryAccessBookingRepository $temporaryAccessBookingRepository
     *
     * @return void
     */
    public function __construct(
        TemporaryAccessBookingRepository $temporaryAccessBookingRepository
    ) {
        $this->temporaryAccessBookingRepository = $temporaryAccessBookingRepository;
    }

    /**
     * Handle the event.
     *
     * @param NewBooking  $event
     *
     * @return void
     */
    public function handle(BookingApproved $event)
    {
        // get a fresh copy of the booking just in case
        $booking = $this->temporaryAccessBookingRepository->findOneById($event->booking->getId());
        $when = Carbon::now()->addMinutes(Meta::getInt('temporary_access_notification_delay', 5));

        if (is_null($booking)) {
            return;
        }

        if (! $booking->isApproved()) {
            // booking is no longer approved??
            return;
        }

        // User
        $booking->getUser()->notify(
            (new BookingApprovedNotification($booking))->delay($when)
        );
    }
}
