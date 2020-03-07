<?php

namespace App\Notifications\Banking\Stripe;

use HMS\Entities\Role;
use HMS\Entities\User;
use Illuminate\Bus\Queueable;
use HMS\Entities\Banking\Stripe\Charge;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SnackspaceRefund extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var Charge
     */
    protected $charge;

    /**
     * @var int
     */
    protected $refundAmount;

    /**
     * Create a new notification instance.
     *
     * @param Charge $charge
     * @param int $refundAmount
     *
     * @return void
     */
    public function __construct(Charge $charge, int $refundAmount)
    {
        $this->charge = $charge;
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
        $amountString = money($this->refundAmount, 'GBP');

        if ($notifiable instanceof User) {
            $balance = $notifiable->getProfile()->getBalance();
            $balanceString = money($balance, 'GBP');

            return (new MailMessage)
                ->subject('Snackspace payment refunded.')
                ->greeting('Hello ' . $notifiable->getFirstname())
                ->line('Your Snackspace payment has been refunded by ' . $amountString)
                ->line('Your balance is now ' . $balanceString)
                ->line('Refund may take 5-10 days to appear on your card\'s statement.');
        } elseif ($notifiable instanceof Role) {
            return (new MailMessage)
                ->subject('Snackspace Stripe payment refunded.')
                ->greeting('Hello ' . $notifiable->getDisplayName())
                ->line(
                    $this->charge->getUser()->getFullname() .
                    '\'s Snackspace payment has been refunded ' . $amountString
                );
        }
    }
}
