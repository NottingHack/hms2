<?php

namespace App\Notifications\Banking\Stripe;

use HMS\Entities\Banking\Stripe\Charge;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Stripe\Dispute;

class DisputeDonationFundsReinstated extends Notification implements ShouldQueue
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

        return (new MailMessage)
            ->subject('Donation payment in dispute, funds reinstated.')
            ->greeting('Hello ' . $notifiable->getDisplayName())
            ->line(
                $this->charge->getUser()->getFullname() .
                '\'s Donation payment is in dispute and ' .
                $amountString . ' has been reinstated.'
            );
    }
}
