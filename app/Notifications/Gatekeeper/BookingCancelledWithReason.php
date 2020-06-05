<?php

namespace App\Notifications\Gatekeeper;

use HMS\Entities\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use HMS\Entities\Gatekeeper\TemporaryAccessBooking;

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
     * @var User
     */
    protected $rejectedBy;

    /**
     * Create a new notification instance.
     *
     * @param TemporaryAccessBooking  $temporaryAccessBooking
     * @param string $reason
     * @param User $rejectedBy
     *
     * @return void
     */
    public function __construct(TemporaryAccessBooking $booking, string $reason, User $rejectedBy)
    {
        $this->booking = $booking;
        $this->reason = $reason;
        $this->rejectedBy = $rejectedBy;
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

        return (new MailMessage)
            ->subject('Nottingham Hackspace: Access booking cancelled')
            ->markdown(
                'emails.gatekeeper.booking_cancelled_with_reason',
                [
                    'buildingName' => $bookableArea->getBuilding()->getName(),
                    'name' => $this->booking->getUser()->getFirstname(),
                    'start' => $this->booking->getStart(),
                    'end' => $this->booking->getEnd(),
                    'bookableAreaName' => $bookableArea->getName(),
                    'reason' => $this->reason,
                    'rejectedBy' => $this->rejectedBy->getFullname(),
                ]
            );
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
