<?php

namespace App\Listeners\Gatekeeper;

use App\Events\Gatekeeper\BookingRejected;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Gatekeeper\BookingRejected as BookingRejectedNotification;
use App\Notifications\Gatekeeper\BookingCancelledWithReason as BookingCancelledWithReasonNotification;

class NotifyBookingRejected implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param NewBooking  $event
     *
     * @return void
     */
    public function handle(BookingRejected $event)
    {
        if ($event->booking->isApproved()) {
            // this was a booking that had been accepted but has now been cancelled with a reason
            // User
            $event->booking->getUser()->notify(
                new BookingCancelledWithReasonNotification($event->booking, $event->reason, $event->rejectedBy)
            );
        } else {
            // this booking was not yet approved
            // User
            $event->booking->getUser()->notify(
                new BookingRejectedNotification($event->booking, $event->reason, $event->rejectedBy)
            );
        }
    }
}
