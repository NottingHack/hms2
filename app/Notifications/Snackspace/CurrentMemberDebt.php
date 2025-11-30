<?php

namespace App\Notifications\Snackspace;

use HMS\Entities\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CurrentMemberDebt extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var int
     */
    protected $latetsTotalDebt;

    /**
     * Create a new notification instance.
     *
     * @param int $latetsTotalDebt
     *
     * @return void
     */
    public function __construct(int $latetsTotalDebt)
    {
        $this->latetsTotalDebt = $latetsTotalDebt;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param User $notifiable
     *
     * @return array
     */
    public function via(User $notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param User $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(User $notifiable)
    {
        return (new MailMessage)
            ->subject(config('branding.space_name') . ': Outstanding Snackspace/Tool-Usage balance')
            ->markdown(
                'emails.snackspace.current_member_debt',
                [
                    'fullname' => $notifiable->getFullname(),
                    'snackspaceBalance' => $notifiable->getProfile()->getBalance(),
                    'latetsTotalDebt' => $this->latetsTotalDebt,
                ]
            );
    }
}
