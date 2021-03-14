<?php

namespace App\Notifications\Governance\Proxy;

use HMS\Entities\Governance\Meeting;
use HMS\Entities\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PrincipalCheckedIn extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var Meeting
     */
    protected $meeting;

    /**
     * @var User
     */
    protected $principal;

    /**
     * Create a new notification instance.
     *
     * @param Proxy $proxy
     *
     * @return void
     */
    public function __construct(Meeting $meeting, User $principal)
    {
        $this->meeting = $meeting;
        $this->principal = $principal;
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
            ->subject('Nottingham Hackspace: Proxy representation cancelled (Checked-in)')
            ->line('This email is to notify you that your representation of ' . $this->principal->getFullname() . ' at the \'' . $this->meeting->getTitle() . '\' has been cancelled as they have check-in at the meeting.');
    }
}
