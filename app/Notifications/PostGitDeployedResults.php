<?php

namespace App\Notifications;

use HMS\Entities\Role;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PostGitDeployedResults extends Notification
{
    use Queueable;

    /**
     * @var array
     */
    protected $commandResults;

    /**
     * Create a new notification instance.
     *
     * @param array $commandResults
     *
     * @return void
     */
    public function __construct($commandResults)
    {
        $this->commandResults = $commandResults;
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
            ->subject('HMS Deploy results for ' . gethostname())
            ->markdown(
                'emails.postGitDeployed',
                [
                'teamName' => ($notifiable instanceof Role) ? $notifiable->getDisplayName() : $notifiable->getName(),
                'commandResults' => $this->commandResults,
                ]
            );
    }
}
