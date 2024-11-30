<?php

namespace App\Notifications\Governance\Proxy;

use Carbon\Carbon;
use HMS\Entities\Governance\Proxy;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class NotifyTrusteesProxyRegistered extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var Proxy
     */
    protected $proxy;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Proxy $proxy)
    {
        $this->proxy = $proxy;
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
        $meeting = $this->proxy->getMeeting();

        $content = 'A new Proxy has been registered. There are now ' . $meeting->getProxies()->count() . ' proxies registered.';

        return (new SlackMessage)
            ->to($notifiable->getSlackChannel())
            ->attachment(fn ($attachment) =>
                $attachment->title($meeting->getTitle() . ': Proxy Registered')
                            ->content($content)
                            ->fallback($content)
                            ->timestamp(Carbon::now())
            );
    }
}
