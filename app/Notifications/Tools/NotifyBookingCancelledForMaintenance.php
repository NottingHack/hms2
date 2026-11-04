<?php

namespace App\Notifications\Tools;

use HMS\Entities\Tools\Tool;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NotifyBookingCancelledForMaintenance extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param Tool $tool
     *
     * @return void
     */
    public function __construct(
        protected Tool $tool
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
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
        return (new MailMessage)
                    ->subject('Booking for ' . $this->tool->getDisplayName() . ' has been cancelled')
                    ->greeting('Hello ' . $notifiable->getFirstname() . ',')
                    ->line(
                        ' Unfortunately your booking for ' . $this->tool->getDisplayName() . ' has been cancelled due to an issue with the tool.'
                    );
    }
}
