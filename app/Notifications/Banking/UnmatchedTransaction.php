<?php

namespace App\Notifications\Banking;

use HMS\Entities\Banking\BankTransaction;
use HMS\Entities\Role;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\View;

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
        $theme = config('mail.markdown.theme');
        $themeWide = $theme . '-wide';

        return (new MailMessage)
            ->theme(View::exists('vendor.mail.html.themes.' . $themeWide) ? $themeWide : $theme)
            ->subject('HMS Unmatched transactions')
            ->markdown('emails.banking.unmatchedTransaction', [
                'teamName' => ($notifiable instanceof Role) ? $notifiable->getDisplayName() : $notifiable->getName(),
                'unmatchedTransaction' => $this->unmatchedTransaction,
                'unmatchedBank' => $this->unmatchedBank,
            ]);
    }
}
