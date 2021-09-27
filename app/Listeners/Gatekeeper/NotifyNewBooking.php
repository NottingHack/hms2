<?php

namespace App\Listeners\Gatekeeper;

use App\Events\Gatekeeper\NewBooking;
use App\Notifications\Gatekeeper\BookingApprovedAutomatically as BookingApprovedAutomaticallyNotification;
use App\Notifications\Gatekeeper\BookingMade as BookingMadeNotification;
use App\Notifications\Gatekeeper\BookingRequested as BookingRequestedNotification;
use Carbon\Carbon;
use HMS\Entities\Role;
use HMS\Facades\Meta;
use HMS\Repositories\Gatekeeper\TemporaryAccessBookingRepository;
use HMS\Repositories\RoleRepository;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyNewBooking implements ShouldQueue
{
    /**
     * @var TemporaryAccessBookingRepository
     */
    protected $temporaryAccessBookingRepository;

    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * Create the event listener.
     *
     * @param TemporaryAccessBookingRepository $temporaryAccessBookingRepository
     * @param RoleRepository $roleRepository
     *
     * @return void
     */
    public function __construct(
        TemporaryAccessBookingRepository $temporaryAccessBookingRepository,
        RoleRepository $roleRepository
    ) {
        $this->temporaryAccessBookingRepository = $temporaryAccessBookingRepository;
        $this->roleRepository = $roleRepository;
    }

    /**
     * Handle the event.
     *
     * @param  NewBooking  $event
     *
     * @return void
     */
    public function handle(NewBooking $event)
    {
        // get a fresh copy of the booking just in case
        $booking = $this->temporaryAccessBookingRepository->findOneById($event->booking->getId());
        $when = Carbon::now()->addMinutes(Meta::getInt('temporary_access_notification_delay', 5));

        if (is_null($booking)) {
            // booking was removed before we get to send the notifications
            return;
        }

        if ($booking->isApproved()) {
            // booking has been approved
            if ($booking->getUser() == $booking->getApprovedBy()) {
                // this booking was self approved (automatic) so notify the user
                $booking->getUser()->notify(
                    (new BookingApprovedAutomaticallyNotification($booking))->delay($when)
                );
            } else {
                // booking was made on behalf of the user by a trustee?
                $booking->getUser()->notify(
                    (new BookingMadeNotification($booking))->delay($when)
                );
            }
        } else {
            // Requires approval
            // Notify Trustees
            $trusteesTeamRole = $this->roleRepository->findOneByName(Role::TEAM_TRUSTEES);
            $trusteesTeamRole->notify(
                (new BookingRequestedNotification($booking))->delay($when)
            );
        }
    }
}
