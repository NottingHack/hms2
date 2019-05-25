<?php

namespace App\Notifications\Membership;

use HMS\Entities\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class YoungHackerTurnedEighteen extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var User
     */
    protected $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
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
        return ['mail', 'slack'];
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
            ->subject('Young Hacker Turned 18')
            ->greeting('Hello ' . $notifiable->getDisplayName())
            ->line($this->user->getFullname() . ' has tunred 18 is and now a full member.')
            ->action('View member', route('users.admin.show', ['user' => $this->user->getId()]));
    }

    /**
     * Get the Slack representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return \Illuminate\Notifications\Messages\SlackMessage
     */
    public function toSlack($notifiable)
    {
        $userId = $this->user->getId();

        return (new SlackMessage)
            ->to($notifiable->getSlackChannel())
            ->attachment(function ($attachment) use ($userId) {
                $attachment->title('Young Hacker Turned 18', route('user.show', ['user' => $userId]))
                            ->content('A young hacker has tunred 18 is and now a full member.')
                            ->fallback(
                                'A young hacker has tunred 18 is and now a full member. <'
                                . route('users.admin.show', ['user' => $userId])
                                . '|review>'
                            )
                            ->timestamp(Carbon::now());
            });
    }
}
