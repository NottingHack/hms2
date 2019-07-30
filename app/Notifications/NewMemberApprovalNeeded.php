<?php

namespace App\Notifications;

use Carbon\Carbon;
use HMS\Entities\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;

class NewMemberApprovalNeeded extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var bool
     */
    protected $rerequest;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user, bool $rerequest = false)
    {
        $this->user = $user;
        $this->rerequest = $rerequest;
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
        return ['slack', 'mail'];
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
        if ($this->rerequest) {
            return (new MailMessage)
                        ->line('A member has updated there details and asked for another review')
                        ->action(
                            'Review and approve member',
                            route('membership.index')
                        );
        }

        return (new MailMessage)
                    ->line('New member approval needed')
                    ->action(
                        'Review and approve member',
                        route('membership.index')
                    )
                    ->line('Please review there details');
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
        if ($this->rerequest) {
            return (new SlackMessage)
                ->to($notifiable->getSlackChannel())
                ->attachment(function ($attachment) use ($userId) {
                    $attachment->title('Review member details', route('membership.approval', ['user' => $userId]))
                                ->content('A member has updated there details and asked for another review.')
                                ->fallback(
                                    'A member has updated there details and asked for another review. <'
                                    . route('membership.index')
                                    . '|review>'
                                )
                                ->timestamp(Carbon::now());
                });
        }

        return (new SlackMessage)
            ->to($notifiable->getSlackChannel())
            ->attachment(function ($attachment) use ($userId) {
                $attachment->title('Review member details', route('membership.index'))
                            ->content('A new member needs approval.')
                            ->fallback(
                                'A new member needs approval. <'
                                . route('membership.index')
                                . '|review>'
                            )
                            ->timestamp(Carbon::now());
            });
    }
}
