<?php

namespace App\Listeners\Gatekeeper;

use HMS\Entities\Role;
use HMS\Repositories\RoleRepository;
use App\Events\Gatekeeper\NewBooking;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Gatekeeper\BookingRequested;
use HMS\Repositories\Gatekeeper\TemporaryAccessBookingRepository;

class NotifyNewBookingRequest implements ShouldQueue
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
     * @param NewBooking  $event
     *
     * @return void
     */
    public function handle(NewBooking $event)
    {
        // get a fresh copy of the booking just in case
        $booking = $this->temporaryAccessBookingRepository->find($event->booking->getId());

        if ($booking->isApproved()) {
            // booking has already been approved
            return;
        }

        // Notify Trustees
        $trusteesTeamRole = $this->roleRepository->findOneByName(Role::TEAM_TRUSTEES);
        $trusteesTeamRole->notify(new BookingRequested($booking));
    }
}
