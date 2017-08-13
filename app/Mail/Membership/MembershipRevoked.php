<?php

namespace App\Mail\Membership;

use HMS\Entities\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use HMS\Repositories\MetaRepository;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MembershipRevoked extends Mailable implements ShouldQueue
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
     * @return void
     */
    public function __construct(User $user, MetaRepository $metaRepository)
    {
        $this->accountNo = $metaRepository->get('so_accountNumber');
        $this->sortCode = $metaRepository->get('so_sortCode');
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
        return $this->subject('Nottingham Hackspace: Your Membership Has Been Revoked.')
                    ->markdown('emails.membership.membershipRevoked');
    }
}
