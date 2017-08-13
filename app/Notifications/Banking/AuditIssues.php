<?php

namespace App\Notifications\Banking;

use HMS\Entities\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AuditIssues extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var User[]
     */
    protected $ohCrapUsers;

    /**
     * Create a new notification instance.
     *
     * @param  User[] $ohCrapUsers
     * @return void
     */
    public function __construct($ohCrapUsers)
    {
        $this->ohCrapUsers = $ohCrapUsers;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'slack'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)->subject('Audit Issues')
                                ->markdown('emails.banking.auditIssues', ['ohCrapUsers' => $ohCrapUsers]);
    }

    /**
     * Get the Slack representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\SlackMessage
     */
    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->to($notifiable->getSlackChannel())
            ->attachment(function ($attachment) use ($userId) {
                $attachment->title('Audit Issues')
                            ->content('There has been a issue during the membership audit.')
                            ->fallback('There has been a issue during the membership audit')
                            ->timestamp(Carbon::now());
            });
    }
}
