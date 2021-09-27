<?php

namespace App\Notifications\Gatekeeper;

use Carbon\Carbon;
use HMS\Entities\Gatekeeper\Building;
use HMS\Entities\Gatekeeper\TemporaryAccessBooking;
use HMS\Entities\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class NotifyTrusteeOverstay extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var Building
     */
    protected $building;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var TemporaryAccessBooking
     */
    protected $booking;

    /**
     * Create a new notification instance.
     *
     * @param Building $building
     * @param TemporaryAccessBooking $booking
     *
     * @return void
     */
    public function __construct(
        Building $building,
        User $user,
        TemporaryAccessBooking $booking
    ) {
        $this->building = $building;
        $this->user = $user;
        $this->booking = $booking;
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
        return ['mail', 'slack'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Temporary Access Overstay: ' . $this->user->getFullname())
                    ->line(
                        'Gatekeeper believes that ' . $this->user->getFullname()
                        . ' is still at ' . $this->building->getName()
                    )
                    ->line('Their booking ended at ' . $this->booking->getEnd()->toDatetimeString());
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
        $content = 'Gatekeeper believes that '
            . $this->user->getFullname() . ' is still at ' . $this->building->getName();

        return (new SlackMessage)
            ->to($notifiable->getSlackChannel())
            ->content('Temporary Access Overstay')
            ->attachment(function ($attachment) use ($content) {
                $attachment->content($content)
                    ->fields([
                        'Start' => $this->booking->getStart()->toDateTimeString(),
                        'End' => $this->booking->getEnd()->toDateTimeString(),
                        'Bookable Area' => $this->booking->getBookableArea()->getName(),
                        'Guests' => $this->booking->getGuests(),
                        'Reason' => $this->booking->getNotes(),
                    ])
                    ->fallback($content)
                    ->timestamp(Carbon::now());
            });
    }
}
