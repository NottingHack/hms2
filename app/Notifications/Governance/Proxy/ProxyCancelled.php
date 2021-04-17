<?php

namespace App\Notifications\Governance\Proxy;

use HMS\Entities\Governance\Meeting;
use HMS\Entities\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProxyCancelled extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var Meeting
     */
    protected $meeting;

    /**
     * @var User
     */
    protected $proxy;

    /**
     * Create a new notification instance.
     *
     * @param Proxy $proxy
     *
     * @return void
     */
    public function __construct(Meeting $meeting, User $proxy)
    {
        $this->meeting = $meeting;
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
        return (new MailMessage)
            ->subject(config('branding.space_name') . ': Proxy vote cancelled')
            ->line('This email is to confirm that your Proxy vote for \'' . $this->meeting->getTitle() . '\' on ' . $this->meeting->getStartTime()->toFormattedDateString() . ' has been cancelled.')
            ->line($this->proxy->getFullname() . ' will no longer be representing you at the meeting.')
            ->line('If you are still not going to make it, please consider finding a new member to designate as your proxy.')
            ->action('Designate a Proxy', route('governance.proxies.link', ['meeting' => $this->meeting->getId()]));
    }
}
