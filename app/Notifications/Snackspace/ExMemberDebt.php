<?php

namespace App\Notifications\Snackspace;

use HMS\Entities\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ExMemberDebt extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var int
     */
    protected $latetsTotalDebt;

    /**
     * @var int
     */
    protected $latetsExDebt;

    /**
     * @var string
     */
    protected $accountNo;

    /**
     * @var string
     */
    protected $sortCode;

    /**
     * Create a new notification instance.
     *
     * @param int $latetsTotalDebt
     * @param int $latetsExDebt
     * @param string $accountNo
     * @param string $sortCode
     *
     * @return void
     */
    public function __construct(
        int $latetsTotalDebt,
        int $latetsExDebt,
        string $accountNo,
        string $sortCode
    ) {
        $this->latetsTotalDebt = $latetsTotalDebt;
        $this->latetsExDebt = $latetsExDebt;
        $this->accountNo = $accountNo;
        $this->sortCode = $sortCode;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param User $notifiable
     *
     * @return array
     */
    public function via(User $notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param User $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(User $notifiable)
    {
        return (new MailMessage)
            ->subject('Nottingham Hackspace: Outstanding Snackspace/Tool-Usage balance')
            ->markdown(
                'emails.snackspace.ex_member_debt',
                [
                    'fullname' => $notifiable->getFullname(),
                    'snackspaceBalance' => $notifiable->getProfile()->getBalance(),
                    'paymentRef' => 'SNACKSPACE-EX' . $notifiable->getId()
                        . $notifiable->getFirstname()[0] . $notifiable->getLastname()[0],
                    'latetsTotalDebt' => $this->latetsTotalDebt,
                    'latetsExDebt' => $this->latetsExDebt,
                    'accountNo' => $this->accountNo,
                    'sortCode' => $this->sortCode,
                ]
            );
    }
}
