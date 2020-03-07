<?php

namespace App\Notifications\Banking\Stripe;

use HMS\Entities\User;
use Illuminate\Bus\Queueable;
use HMS\Entities\Banking\Stripe\Charge;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SnackspacePayment extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var Charge
     */
    protected $charge;

    /**
     * Create a new notification instance.
     *
     * @param Charge $charge
     *
     * @return void
     */
    public function __construct(Charge $charge)
    {
        $this->charge = $charge;
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
        if (! $notifiable instanceof User) {
            return;
        }

        if (! $notifiable->getProfile()) {
            // Should really not be here
            return;
        }

        $amount = $this->charge->getAmount();
        $amountString = money($amount, 'GBP');

        $balance = $notifiable->getProfile()->getBalance();
        $balanceString = money($balance, 'GBP');

        return (new MailMessage)
            ->subject('Snackspace card payment complete.')
            ->greeting('Hello ' . $notifiable->getFirstname())
            ->line('Your Snackspace payment of ' . $amountString . ' has been successful.')
            ->line('Your balance is now ' . $balanceString)
            ->line('Thank you for your payment.');
    }
}
