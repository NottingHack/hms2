<?php

namespace App\Notifications\Gatekeeper;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use HMS\Entities\Gatekeeper\TemporaryAccessBooking;

class BookingApproved extends Notification implements ShouldQueue
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
     * @param mixed $notifiable
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
     * @param mixed $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $bookableArea = $this->booking->getBookableArea();

        return (new MailMessage)
            ->subject('Nottingham Hackspace: Access booking request approved')
            ->markdown(
                'emails.gatekeeper.booking_approved',
                [
                    'name' => $this->booking->getUser()->getFirstname(),
                    'buildingName' => $bookableArea->getBuilding()->getName(),
                    'bookableAreaName' => $bookableArea->getName(),
                    'start' => $this->booking->getStart(),
                    'end' => $this->booking->getEnd(),
                ]
            );
    }
}
