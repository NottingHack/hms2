<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NotifyIncommingRoleEmail extends Notification
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
        $message = <<<'EOF'
        A new email has arrived.
        EOF;

        return DiscordMessage::create($message);
    }
}
