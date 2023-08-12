<?php

namespace App\Events\Banking;

use HMS\Entities\Banking\BankTransaction;
use HMS\Entities\User;
use Illuminate\Queue\SerializesModels;

class ExMemberPaymentUnderMinimum
{
    use SerializesModels;

    /**
     * @var User
     */
    public $user;

    /**
     * @var BankTransaction
     */
    public $latestTransaction;

    /**
     * Create a new event instance.
     *
     * @param User $user
     * @param BankTransaction $latestTransaction
     *
     * @return void
     */
    public function __construct(User $user, BankTransaction $latestTransaction)
    {
        $this->user = $user;
        $this->latestTransaction = $latestTransaction;
    }
}
