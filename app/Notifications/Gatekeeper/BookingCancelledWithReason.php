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

class BookingCancelledWithReason extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var TemporaryAccessBooking
     */
    protected $booking;

    /**
     * @var string
     */
    protected $reason;

    /**
     * Create a new notification instance.
     *
     * @param TemporaryAccessBooking  $temporaryAccessBooking
     * @param string $reason
     *
     * @return void
     */
    public function __construct(TemporaryAccessBooking $booking, string $reason)
    {
        $this->booking = $booking;
        $this->reason = $reason;
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
            ->subject(config('branding.space_name') . ': Access Booking Cancelled')
            ->markdown(
                'emails.gatekeeper.booking_cancelled_with_reason',
                [
                    'buildingName' => $bookableArea->getBuilding()->getName(),
                    'name' => $this->booking->getUser()->getFirstname(),
                    'start' => $this->booking->getStart(),
                    'end' => $this->booking->getEnd(),
                    'bookableAreaName' => $bookableArea->getName(),
                    'guests' => $this->booking->getGuests(),
                    'reason' => $this->reason,
                ]
            );
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     *
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
