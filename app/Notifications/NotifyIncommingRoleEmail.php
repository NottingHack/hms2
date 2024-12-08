<?php

namespace App\Notifications;

use HMS\Entities\Role;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Discord\DiscordChannel;
use NotificationChannels\Discord\DiscordMessage;

class NotifyIncommingRoleEmail extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param Role $role
     * @param string $folder
     * @param string $event
     * @param string $from
     * @param string $subject
     * @param string $snippet
     * @param int $messages
     * @param int $unseen
     */
    public function __construct(
        protected Role $role,
        protected string $folder,
        protected string $event,
        protected string $from,
        protected string $subject,
        protected string $snippet,
        protected int $messages,
        protected int $unseen,
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
        $channels = [];
        if (config('services.discord.token')) {
            $channels[] = DiscordChannel::class;
        }

        return $channels;
    }

    /**
     * Get the Discord representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return NotificationChannels\Discord\DiscordMessage
     */
    public function toDiscord($notifiable)
    {
        $url = route('teams.show', $this->role->getId());

        $embed = [
            'title' => '📬 New Team Email',
            'url' => $url,
            'fields' => [
                [
                    'name' => 'Subject',
                    'value' => $this->subject,
                    'inline' => false,
                ],
                [
                    'name' => 'Message Count',
                    'value' => $this->unseen . ' unread of ' . $this->messages,
                    'inline' => false,
                ],
            ],
        ];

        return (new DiscordMessage())->embed($embed);
    }
}
