<?php

namespace App\Notifications\Governance\Proxy;

use Carbon\Carbon;
use HMS\Entities\Governance\Meeting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class NotifyTrusteesProxyCancelled extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var Meeting
     */
    protected $meeting;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Meeting $meeting)
    {
        $this->meeting = $meeting;
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
        $content = 'A Proxy has been cancelled. There are now ' . $this->meeting->getProxies()->count() . ' proxies registered.';

        return (new SlackMessage)
            ->to($notifiable->getSlackChannel())
            ->attachment(fn ($attachment) => $attachment->title($this->meeting->getTitle() . ': Proxy Cancelled')
                            ->content($content)
                            ->fallback($content)
                            ->timestamp(Carbon::now())
            );
    }
}
