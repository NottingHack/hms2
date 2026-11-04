<?php

namespace App\Notifications\Forms;

use App\Notifications\NotificationSensitivityInterface;
use App\Notifications\NotificationSensitivityType;
use HMS\Entities\Forms\Form;
use HMS\Entities\Forms\FormResponse;
use HMS\Entities\Role;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Discord\DiscordChannel;
use NotificationChannels\Discord\DiscordMessage;

class NewFormResponse extends Notification implements ShouldQueue, NotificationSensitivityInterface
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param Role $role
     */
    public function __construct(
        protected Role $role,
        protected Form $form,
        protected FormResponse $formResponse
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
     * @return DiscordMessage
     */
    public function toDiscord($notifiable)
    {
        $url = route('forms.responses', $this->form->getId());

        $embed = [
            'title' => '🖇️ Form Response: ' . $this->form->getName(),
            'url' => $url,
            'description' => $this->formResponse->getResponder()->getFullName() . ' has responded to your form.',
        ];

        return (new DiscordMessage())->embed($embed);
    }

    /**
     * Returns the sensitivity for notification routing to
     * Discord. e.g. whether it should go to the private or public
     * team channel.
     *
     * @return string
     */
    public function getDiscordSensitivity()
    {
        return NotificationSensitivityType::PRIVATE;
    }
}
