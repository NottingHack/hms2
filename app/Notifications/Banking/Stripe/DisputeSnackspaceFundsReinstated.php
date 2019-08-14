<?php

namespace App\Notifications\Banking\Stripe;

use Stripe\Dispute;
use HMS\Entities\Role;
use HMS\Entities\User;
use Illuminate\Bus\Queueable;
use HMS\Entities\Banking\Stripe\Charge;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class DisputeSnackspaceFundsReinstated extends Notification implements ShouldQueue
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

        if ($notifiable instanceof User) {
            $balance = $notifiable->getProfile()->getBalance();
            $balanceString = money_format('%n', $balance / 100);

            return (new MailMessage)
                ->subject('Snackspace card payment in dispute, funds reinstated.')
                ->greeting('Hello ' . $notifiable->getFirstname())
                ->line(
                    'Your Snackspace card payment is in dispute and ' .
                    $amountString . ' has been reinstated to your balance.'
                )
                ->line('Your balance is now ' . $balanceString);
        } elseif ($notifiable instanceof Role) {
            return (new MailMessage)
                ->subject('Snackspace Stripe payment in dispute, funds reinstated.')
                ->greeting('Hello ' . $notifiable->getDisplayName())
                ->line(
                    $this->charge->getUser()->getFullname() .
                    '\'s Snackspace payment is in dispute and ' .
                    $amountString . ' has been reinstated to their balance.'
                );
        }
    }
}
