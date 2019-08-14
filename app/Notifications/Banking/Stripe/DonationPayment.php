<?php

namespace App\Notifications\Banking\Stripe;

use HMS\Entities\Role;
use HMS\Entities\User;
use Illuminate\Bus\Queueable;
use HMS\Entities\Banking\Stripe\Charge;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class DonationPayment extends Notification implements ShouldQueue
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
        $amount = $this->charge->getAmount();
        $amountString = money_format('%n', $amount / 100);

        if ($notifiable instanceof User) {
            return (new MailMessage)
                ->subject('Donation received.')
                ->greeting('Hello ' . $notifiable->getFirstname())
                ->line('Thank you for your Donation of ' . $amountString . ' to Nottingham Hackspace.');
        } elseif ($notifiable instanceof Role) {
            return (new MailMessage)
                ->subject('Donation received (via Stripe).')
                ->greeting('Hello ' . $notifiable->getDisplayName())
                ->line(
                    $this->charge->getUser()->getFullname() .
                    ' has made a donation of ' . $amountString .
                    ' via Stripe.'
                );
        }
    }
}
