<?php

namespace App\Listeners\Gatekeeper;

use Carbon\Carbon;
use HMS\Facades\Meta;
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
        $user = $event->booking->getUser();
        $when = Carbon::now()->addMinutes(Meta::getInt('temporary_access_notification_delay', 5));

        if ($event->booking->isApproved()) {
            // this was a booking that had been accepted but has now been cancelled with a reason
            // User
            $user->notify(
                (new BookingCancelledWithReasonNotification($event->booking, $event->reason)) //->delay($when)
            );
        } else {
            // this booking was not yet approved
            // User
            $user->notify(
                (new BookingRejectedNotification($event->booking, $event->reason)) //->delay($when)
            );
        }
    }
}
