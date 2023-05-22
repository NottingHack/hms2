<?php

namespace App\Notifications\Users;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Discord\DiscordChannel;
use NotificationChannels\Discord\DiscordMessage;

class PasswordChanged extends Notification implements ShouldQueue
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
        return (new MailMessage)
                    ->subject('Your HMS password has been changed')
                    ->greeting('Hello ' . $notifiable->getFirstname() . ',')
                    ->line(
                        'Your password for signing into HMS was changed recently.'
                        . 'If you made this change then ignore me!'
                    )
                    ->line("If, however, you didn't change your password please")
                    ->action('Reset Password Here', route('password.request'))
                    ->line('If you have any problems please reply to this email.');
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
        $link = route('password.request');

        $message = <<<EOF
        __**Hackspace Account Password Changed**__

        Your password for signing into HMS was changed recently.

        If you made this change then ignore me! Otherwise, you can reset your password here

        $link
        EOF;

        return DiscordMessage::create($message);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
