<?php

namespace App\Notifications;

use HMS\Entities\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewMemberApprovalNeeded extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var bool
     */
    protected $rerequest;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user, bool $rerequest = false)
    {
        $this->user = $user;
        $this->rerequest = $rerequest;
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
        if ($this->rerequest) {
            return (new MailMessage)
                        ->line('A member has updated there detials and asked for another review')
                        ->action('Review and approve member', route('membership.approval', ['user' => $this->user->getId()]));
        }

        return (new MailMessage)
                    ->line('New member approval needed')
                    ->action('Review and approve member', route('membership.approval', ['user' => $this->user->getId()]))
                    ->line('Please review there detials');
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
