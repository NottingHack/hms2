<?php

namespace App\Notifications\Banking\Stripe;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Spatie\WebhookClient\Models\WebhookCall;

class ProcessingIssue extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var WebhookCall
     */
    protected $webhookCall;

    /**
     * @var string
     */
    protected $description;

    /**
     * Create a new notification instance.
     *
     * @param WebhookCall $webhookCall
     * @param string $description
     *
     * @return void
     */
    public function __construct(WebhookCall $webhookCall, string $description)
    {
        $this->webhookCall = $webhookCall;
        $this->description = $description;
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
        return ['mail'];
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
            ->subject('Stripe webhook processing issue')
            ->line('There was an issue processing a stripe payment during ' . $this->description . '.')
            ->line('WebhookCall ID is: ' . $this->webhookCall->id);
    }
}
