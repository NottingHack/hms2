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

class DonationPayment extends Notification implements ShouldQueue
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
     * Create a new notification instance.
     *
     * @param Charge $charge
     * @param StripeCharge $stripeCharge Stripe/Charge instance
     *
     * @return void
     */
    public function __construct(Charge $charge, StripeCharge $stripeCharge)
    {
        $this->charge = $charge;
        $this->stripeCharge = $stripeCharge;
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
        $amount = $this->charge->getAmount();
        $amountString = money($amount, 'GBP');

        if ($notifiable instanceof User) {
            return (new MailMessage)
                ->subject('Donation received')
                ->greeting('Hello ' . $notifiable->getFirstname())
                ->line('Thank you for your Donation of ' . $amountString . ' to Nottingham Hackspace.');
        } elseif ($notifiable instanceof AnonymousNotifiable) {
            return (new MailMessage)
                ->subject('Donation received')
                ->greeting('Hello ' . $this->stripeCharge->billing_details->name)
                ->line('Thank you for your Donation of ' . $amountString . ' to Nottingham Hackspace.');
        } elseif ($notifiable instanceof Role) {
            if ($this->charge->getUser()) {
                $fullname = $this->charge->getUser()->getFullname();
            } else {
                $fullname = $this->stripeCharge->billing_details->name;
            }

            return (new MailMessage)
                ->subject('Donation received (via Stripe)')
                ->greeting('Hello ' . $notifiable->getDisplayName())
                ->line(
                    $fullname .
                    ' has made a donation of ' . $amountString .
                    ' via Stripe.'
                );
        }
    }
}
