<?php

namespace App\Notifications\Gatekeeper;

use Carbon\Carbon;
use HMS\Entities\Role;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\URL;
use HMS\Repositories\RoleRepository;
use HMS\Entities\Gatekeeper\Building;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use HMS\Entities\Gatekeeper\TemporaryAccessBooking;

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

        $roleRepository = \App::make(RoleRepository::class);
        $trusteesTeamRole = $roleRepository->findOneByName(Role::TEAM_TRUSTEES);
        $trusteesEmail = $trusteesTeamRole->getEmail();

        return (new MailMessage)
            ->from($trusteesEmail, 'Nottingham Hackspace Trustees')
            ->subject('Nottingham Hackspace: Access Booking Ended')
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
