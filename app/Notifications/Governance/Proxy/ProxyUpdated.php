<?php

namespace App\Notifications\Governance\Proxy;

use HMS\Entities\Governance\Proxy;
use HMS\Entities\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProxyUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var Proxy
     */
    protected $proxy;

    /**
     * @var User
     */
    protected $oldProxy;

    /**
     * Create a new notification instance.
     *
     * @param Proxy $proxy
     * @param User $oldProxy
     *
     * @return void
     */
    public function __construct(Proxy $proxy, User $oldProxy)
    {
        $this->proxy = $proxy;
        $this->oldProxy = $oldProxy;
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
            ->subject('Nottingham Hackspace: Proxy vote updated')
            ->line('This email is to confirm that your Proxy vote for \'' . $meeting->getTitle() . '\' on ' . $meeting->getStartTime()->toFormattedDateString() . ' has been updated.')
            ->line($this->proxy->getProxy()->getFullname() . ' has now accepted your Proxy vote')
            ->line($this->oldProxy->getFullname() . ' has been notified that they no longer be need to represent you.')
            ->line('If the meeting will include items to vote on make sure to talk over your opinions and how you wish your proxy votes to be cast.');
    }
}
