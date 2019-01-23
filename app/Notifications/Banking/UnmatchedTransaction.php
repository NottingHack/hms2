<?php

namespace App\Notifications\Banking;

use HMS\Entities\Role;
use Illuminate\Bus\Queueable;
use HMS\Entities\Banking\BankTransaction;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class UnmatchedTransaction extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var BankTransaction[]
     */
    protected $unmatchedTransaction;

    /**
     * @var array
     */
    protected $unmatchedBank;

    /**
     * Create a new notification instance.
     *
     * @param BankTransaction[] $unmatchedTransaction
     * @param array $unmatchedBank
     */
    public function __construct($unmatchedTransaction, $unmatchedBank)
    {
        $this->unmatchedTransaction = $unmatchedTransaction;
        $this->unmatchedBank = $unmatchedBank;
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
        return (new MailMessage)
            ->subject('HMS Unmatched transactions')
            ->markdown('emails.banking.unmatchedTransaction', [
                'teamName' => ($notifiable instanceof Role) ? $notifiable->getDisplayName() : $notifiable->getName(),
                'unmatchedTransaction' => $this->unmatchedTransaction,
                'unmatchedBank' => $this->unmatchedBank,
            ]);
    }
}
