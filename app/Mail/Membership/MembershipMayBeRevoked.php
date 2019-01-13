<?php

namespace App\Mail\Membership;

use HMS\Entities\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use HMS\Repositories\MetaRepository;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use HMS\Repositories\Banking\BankRepository;

class MembershipMayBeRevoked extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var string
     */
    public $accountNo;

    /**
     * @var string
     */
    public $sortCode;

    /**
     * @var string
     */
    public $fullname;

    /**
     * @var string
     */
    public $paymentRef;

    /**
     * Create a new message instance.
     *
     * @param User           $user
     * @param MetaRepository $metaRepository
     */
    public function __construct(User $user, MetaRepository $metaRepository, BankRepository $bankRepository)
    {
        $bank = $bankRepository->find($metaRepository->get('so_bank_id'));
        $this->accountNo = $bank->getAccountNumber();
        $this->sortCode = $bank->getSortCode();
        $this->fullname = $user->getFullname();
        $this->paymentRef = $user->getAccount()->getPaymentRef();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Nottingham Hackspace: Your Membership May Be Revoked.')
                    ->markdown('emails.membership.membershipMayBeRevoked');
    }
}
