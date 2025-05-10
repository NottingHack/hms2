<?php

namespace App\Notifications\Users;

use Carbon\Carbon;
use HMS\Entities\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class NotifyTrusteesProfileUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var User
     */
    protected $user;

    /**
     * @car string[]
     */
    protected $changes;

    /**
     * Create a new notification instance.
     *
     * @param Profile $profile
     * @param string[] $changes
     *
     * @return void
     */
    public function __construct(User $user, $changes)
    {
        $this->user = $user;
        $this->changes = $changes;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        return ['slack'];
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
        $content = $this->user->getFullname() . " has updated their profile.\n\n" . implode(", ", $this->changes);

        return (new SlackMessage)
           ->attachment(
               fn ($attachment) => $attachment->title('Profile Updated', route('users.admin.show', ['user' => $this->user->getId()]))
                           ->content($content)
                           ->fallback($content)
                           ->timestamp(Carbon::now())
           );
    }
}
