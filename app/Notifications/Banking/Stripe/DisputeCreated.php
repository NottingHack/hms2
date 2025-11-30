<?php

namespace App\Notifications\Banking\Stripe;

use Carbon\Carbon;
use DateTimeZone;
use HMS\Entities\Banking\Stripe\Charge;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Stripe\Dispute;

class DisputeCreated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var Charge
     */
    protected $charge;

    /**
     * @var Dispute
     */
    protected $stripeDispute;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Charge $charge, Dispute $stripeDispute)
    {
        $this->charge = $charge;
        $this->stripeDispute = $stripeDispute;
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
        $amount = $this->stripeDispute->amount;
        $amountString = money($amount, 'GBP');

        $reason = $this->stripeDispute->reason;

        // evidenceDueBy in our timezone
        // @phpstan-ignore property.notFound
        $evidenceDueBy = Carbon::createFromTimestamp($this->stripeDispute->evidence_details->due_by)
            ->setTimezone(new DateTimeZone(date_default_timezone_get()));

        return (new MailMessage)
            ->subject('Stripe dispute has been raised')
            ->greeting('Hello ' . $notifiable->getDisplayName())
            ->line(
                'A dispute has been opened against the ' .
                $this->charge->getTypeString() . ' payment of ' . $this->charge->getUser()->getFullname()
            )
            ->line('The disputed amount is ' . $amountString)
            ->line('Reason given is ' . $reason)
            ->line('Evidence is due by ' . $evidenceDueBy->toDateTimeString());
    }
}
