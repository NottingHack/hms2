<?php

namespace App\Mail\Membership;

use HMS\Entities\User;
use HMS\Repositories\Banking\BankRepository;
use HMS\Repositories\Members\BoxRepository;
use HMS\Repositories\MetaRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

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
     * @var int
     */
    public $boxCount;

    /**
     * @var int
     */
    public $snackspaceBalance;

    /**
     * @var string
     */
    public $snackspaceRef;

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @param MetaRepository $metaRepository
     * @param BankRepository $bankRepository
     * @param BoxRepository $boxRepository
     */
    public function __construct(
        User $user,
        MetaRepository $metaRepository,
        BankRepository $bankRepository,
        BoxRepository $boxRepository
    ) {
        $bank = $bankRepository->find($metaRepository->get('so_bank_id'));
        $this->accountNo = $bank->getAccountNumber();
        $this->sortCode = $bank->getSortCode();
        $this->accountName = $bank->getAccountName();
        $this->fullname = $user->getFullname();
        $this->paymentRef = $user->getAccount()->getPaymentRef();
        $this->boxCount = $boxRepository->countInUseByUser($user);
        $this->snackspaceBalance = $user->getProfile()->getBalance();
        $snackspaceRef = 'SNACK-EX' . $user->getId()
            . $user->getFirstname()[0] . $user->getLastname()[0];
        $this->snackspaceRef = preg_replace('/[^-a-zA-Z0-9]/', '', $snackspaceRef);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Nottingham Hackspace: Your Membership May Be Revoked')
                    ->markdown('emails.membership.membershipMayBeRevoked');
    }
}
