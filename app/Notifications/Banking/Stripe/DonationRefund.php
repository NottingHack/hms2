<?php

namespace App\Notifications\Banking\Stripe;

use HMS\Entities\Banking\Stripe\Charge;
use HMS\Entities\Role;
use HMS\Entities\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Stripe\Charge as StripeCharge;

class DonationRefund extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var Charge
     */
    protected $charge;

    /**
     * @var StripeCharge
     */
    protected $stripeCharge;

    /**
     * @var int
     */
    protected $refundAmount;

    /**
     * Create a new notification instance.
     *
     * @param Charge $charge
     * @param StripeCharge $stripeCharge Stripe/Charge instance
     * @param int $refundAmount
     *
     * @return void
     */
    public function __construct(Charge $charge, StripeCharge $stripeCharge, int $refundAmount)
    {
        $this->charge = $charge;
        $this->stripeCharge = $stripeCharge;
        $this->refundAmount = $refundAmount;
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
        if ($notifiable instanceof User
            || $notifiable instanceof AnonymousNotifiable
            || $notifiable instanceof Role
        ) {
            return ['mail'];
        }

        return [];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage|void
     */
    public function toMail($notifiable)
    {
        $amountString = money($this->refundAmount, 'GBP');

        if ($notifiable instanceof User) {
            return (new MailMessage)
                ->subject('Donation refunded')
                ->greeting('Hello ' . $notifiable->getFirstname())
                ->line('Your donation has been refunded by ' . $amountString);
        } elseif ($notifiable instanceof AnonymousNotifiable) {
            return (new MailMessage)
                ->subject('Donation refunded')
                ->greeting('Hello ' . $this->stripeCharge->billing_details->name)
                ->line('Your donation has been refunded by ' . $amountString);
        } elseif ($notifiable instanceof Role) {
            if ($this->charge->getUser()) {
                $fullname = $this->charge->getUser()->getFullname();
            } else {
                $fullname = $this->stripeCharge->billing_details->name;
            }

            return (new MailMessage)
                ->subject('Donation refunded')
                ->greeting('Hello ' . $notifiable->getDisplayName())
                ->line(
                    $fullname .
                    '\'s Donation has been refunded ' . $amountString
                );
        }
    }
}
