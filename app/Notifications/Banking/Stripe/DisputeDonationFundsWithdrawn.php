<?php

namespace App\Notifications\Banking\Stripe;

use Stripe\Dispute;
use Illuminate\Bus\Queueable;
use HMS\Entities\Banking\Stripe\Charge;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class DisputeDonationFundsWithdrawn extends Notification
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
        $amountString = money_format('%n', $amount / 100);

        return (new MailMessage)
            ->subject('Donation payment in dispute, funds withdrawn.')
            ->greeting('Hello ' . $notifiable->getDisplayName())
            ->line(
                $this->charge->getUser()->getFullname() .
                '\'s Donation payment is in dispute and ' .
                $amountString . ' has been withdrawn.'
            );
    }
}
