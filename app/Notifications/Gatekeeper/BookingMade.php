<?php

namespace App\Notifications\Gatekeeper;

use HMS\Entities\Gatekeeper\TemporaryAccessBooking;
use HMS\Entities\Role;
use HMS\Repositories\RoleRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\App;

class BookingMade extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var TemporaryAccessBooking
     */
    protected $booking;

    /**
     * Create a new notification instance.
     *
     * @param TemporaryAccessBooking  $temporaryAccessBooking
     *
     * @return void
     */
    public function __construct(TemporaryAccessBooking $booking)
    {
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

        $roleRepository = App::make(RoleRepository::class);
        $trusteesTeamRole = $roleRepository->findOneByName(Role::TEAM_TRUSTEES);
        $trusteesEmail = $trusteesTeamRole->getEmail();

        return (new MailMessage)
            ->from($trusteesEmail, $trusteesTeamRole->getDisplayName())
            ->subject(config('branding.space_name') . ': Access Booking Created')
            ->markdown(
                'emails.gatekeeper.booking_made',
                [
                    'name' => $this->booking->getUser()->getFirstname(),
                    'buildingName' => $bookableArea->getBuilding()->getName(),
                    'bookableAreaName' => $bookableArea->getName(),
                    'guests' => $this->booking->getGuests(),
                    'start' => $this->booking->getStart(),
                    'end' => $this->booking->getEnd(),
                ]
            );
    }
}
