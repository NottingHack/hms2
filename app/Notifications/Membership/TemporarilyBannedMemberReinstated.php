<?php

namespace App\Notifications\Membership;

use HMS\Entities\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TemporarilyBannedMemberReinstated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var User
     */
    protected $authorizer;

    /**
     * Create a new notification instance.
     *
     * @param User $user Member effected
     * @param User $authorizer Member who clicked the button
     *
     * @return void
     */
    public function __construct(User $user, User $authorizer)
    {
        $this->user = $user;
        $this->authorizer = $authorizer;
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
            ->subject('Temporarily Banned Member Reinstated')
            ->greeting('Hello ' . $notifiable->getDisplayName())
            ->line($this->user->getFullname() . ' has just been reinstated from their temporary ban by '
                . $this->authorizer->getFullname())
            ->action('View member', route('users.admin.show', ['user' => $this->user->getId()]));
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
