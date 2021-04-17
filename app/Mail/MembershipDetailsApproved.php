<?php

namespace App\Mail;

use HMS\Entities\User;
use HMS\Repositories\Banking\BankRepository;
use HMS\Repositories\MetaRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MembershipDetailsApproved extends Mailable implements ShouldQueue
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
    public $accountName;

    /**
     * @var string
     */
    public $fullname;

    /**
     * @var string
     */
    public $paymentRef;

    /**
     * Create a new notification instance.
     *
     * @param User $user
     * @param MetaRepostiory $metaRepository
     * @param BankRepository $bankRepository
     */
    public function __construct(
        User $user,
        MetaRepository $metaRepository,
        BankRepository $bankRepository
    ) {
        $bank = $bankRepository->find($metaRepository->get('so_bank_id'));
        $this->accountNo = $bank->getAccountNumber();
        $this->sortCode = $bank->getSortCode();
        $this->accountName = $bank->getAccountName();
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
        return $this
            ->subject(config('branding.space_name') . ': Please setup your standing order')
            ->markdown('emails.membership.approved');
    }
}
