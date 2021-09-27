<?php

namespace App\Notifications\Gatekeeper;

use Carbon\Carbon;
use HMS\Entities\Gatekeeper\Building;
use HMS\Entities\Gatekeeper\TemporaryAccessBooking;
use HMS\Entities\Role;
use HMS\Repositories\RoleRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;

class NotifyUserOverstay extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var Building
     */
    protected $building;

    /**
     * @var TemporaryAccessBooking
     */
    protected $booking;

    /**
     * Create a new notification instance.
     *
     * @param Building $building
     * @param TemporaryAccessBooking $booking
     *
     * @return void
     */
    public function __construct(
        Building $building,
        TemporaryAccessBooking $booking
    ) {
        $this->building = $building;
        $this->booking = $booking;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $bookableArea = $this->booking->getBookableArea();

        $haveLeftURL = URL::temporarySignedRoute(
            'gatekeeper.building.user.have-left',
            Carbon::now()->addDays(2),
            [
                'building' => $this->building->getId(),
                'user' => $notifiable->getId(),
            ]
        );

        $roleRepository = App::make(RoleRepository::class);
        $trusteesTeamRole = $roleRepository->findOneByName(Role::TEAM_TRUSTEES);
        $trusteesEmail = $trusteesTeamRole->getEmail();

        return (new MailMessage)
            ->from($trusteesEmail, $trusteesTeamRole->getDisplayName())
            ->subject(config('branding.space_name') . ': Access Booking Ended')
            ->markdown(
                'emails.gatekeeper.booking_ended',
                [
                    'name' => $this->booking->getUser()->getFirstname(),
                    'buildingName' => $bookableArea->getBuilding()->getName(),
                    'bookableAreaName' => $bookableArea->getName(),
                    'guests' => $this->booking->getGuests(),
                    'start' => $this->booking->getStart(),
                    'end' => $this->booking->getEnd(),
                    'actionUrl' => $haveLeftURL,
                ]
            );
    }
}
