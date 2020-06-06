<?php

namespace App\Notifications\Gatekeeper;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use HMS\Entities\Gatekeeper\TemporaryAccessBooking;
use Illuminate\Notifications\Messages\SlackMessage;

class BookingRequested extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var TemporaryAccessBooking
     */
    protected $booking;

    /**
     * @var HMS\Entities\User
     */
    protected $user;

    /**
     * @var HMS\Entities\Gatekeeper\BookableArea
     */
    protected $bookableArea;

    /**
     * @var HMS\Entities\Gatekeeper\Building
     */
    protected $building;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(TemporaryAccessBooking $booking)
    {
        $this->booking = $booking;
        $this->user = $booking->getUser();
        $this->bookableArea = $booking->getBookableArea();
        $this->building = $this->bookableArea->getBuilding();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed  $notifiable
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
     * @param mixed  $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Temporary Access Request: ' . $this->user->getFullname())
            ->markdown(
                'emails.gatekeeper.booking_requested',
                [
                    'buildingName' => $this->building->getName(),
                    'userFullName' => $this->user->getFullname(),
                    'start' => $this->booking->getStart(),
                    'end' => $this->booking->getEnd(),
                    'bookableAreaName' => $this->bookableArea->getName(),
                    'reason' => $this->booking->getNotes(),
                    'actionUrl' => route(
                        'gatekeeper.temporary-access',
                        ['date' => $this->booking->getStart()->toIsoString()]
                    ) . '#' . Str::slug($this->building->getName()),
                ]
            );
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
        $content = $this->user->getFullname() . ' has requested access to ' . $this->building->getName();

        return (new SlackMessage)
            ->to($notifiable->getSlackChannel())
            ->content('Temporary Access Request')
            ->attachment(function ($attachment) use ($content) {
                $attachment->content($content)
                    ->fields([
                        'Start' => $this->booking->getStart()->toDateTimeString(),
                        'End' => $this->booking->getEnd()->toDateTimeString(),
                        'Bookable Area' => $this->bookableArea->getName(),
                        'Reason' => $this->booking->getNotes(),
                    ])
                    ->title(
                        'Review booking',
                        route(
                            'gatekeeper.temporary-access',
                            ['date' => $this->booking->getStart()->toIsoString()]
                        ) . '#' . Str::slug($this->building->getName())
                    )
                    ->fallback($content)
                    ->timestamp(Carbon::now());
            });
    }
}
