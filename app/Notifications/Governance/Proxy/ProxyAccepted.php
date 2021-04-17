<?php

namespace App\Notifications\Governance\Proxy;

use HMS\Entities\Governance\Proxy;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProxyAccepted extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var Proxy
     */
    protected $proxy;

    /**
     * Create a new notification instance.
     *
     * @param Proxy $proxy
     *
     * @return void
     */
    public function __construct(Proxy $proxy)
    {
        $this->proxy = $proxy;
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
        $meeting = $this->proxy->getMeeting();

        return (new MailMessage)
            ->subject(config('branding.space_name') . ': Proxy vote accepted')
            ->line('This email is to confirm that your Proxy vote for \'' . $meeting->getTitle() . '\' on ' . $meeting->getStartTime()->toFormattedDateString() . ' has been accepted by:')
            ->line($this->proxy->getProxy()->getFullname())
            ->line('If the meeting will include items to vote on make sure to talk over your opinions and how you wish your proxy votes to be cast.');
    }
}
