<?php

namespace App\Notifications\Users;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Discord\DiscordChannel;
use NotificationChannels\Discord\DiscordMessage;

class DiscordRegistered extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        $channels = ['mail'];
        if (config('services.discord.token')) {
            array_push($channels, DiscordChannel::class);
        }

        return $channels;
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
        if (! $notifiable->getProfile()->getDiscordUsername()) {
            return;
        }

        return (new MailMessage)
                    ->subject('Your HMS account has been linked to Discord')
                    ->greeting('Hello ' . $notifiable->getFirstname() . ',')
                    ->line(
                        'Your hackspace account has been linked to a Discord account.'
                      . 'If you made this change then ignore me!'
                    )
                    ->line("If, however, you didn't, contact a trustee!");
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
        $username = $notifiable->getUsername();

        $message = <<<EOF
        __**Hackspace Account Linked to Discord**__

        Your Discord account has been linked to the HMS profile $username. If you did not do this, contact a trustee, including the username mentioned above.

        Have fun!
        EOF;

        return DiscordMessage::create($message);
    }
}
