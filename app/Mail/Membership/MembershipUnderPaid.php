<?php

namespace App\Mail\Membership;

use HMS\Entities\Role;
use HMS\Entities\User;
use HMS\Repositories\MetaRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MembershipUnderPaid extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /*
     * @var string
     */
    public $fullname;

    /**
     * @var int
     */
    public $minimumAmount;

    /**
     * @var string
     */
    public $membershipTeamEmail;

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @param MetaRepository $metaRepository
     */
    public function __construct(User $user, MetaRepository $metaRepository)
    {
        $this->fullname = $user->getFullname();

        $this->minimumAmount = $metaRepository->getInt('audit_minimum_amount', 200);

        $this->membershipTeamEmail = $roleRepository->findOneByName(Role::TEAM_MEMBERSHIP)->getEmail();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(config('branding.space_name') . ': Membership Payment Under Minimum')
                    ->markdown('emails.membership.membershipUnderPaid');
    }
}
