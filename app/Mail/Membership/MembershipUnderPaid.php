<?php

namespace App\Mail\Membership;

use HMS\Entities\Role;
use HMS\Entities\User;
use HMS\Repositories\MetaRepository;
use HMS\Repositories\RoleRepository;
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
     * @var int
     */
    public $recommendedAmount;

    /**
     * @var string
     */
    public $membershipTeamEmail;

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @param MetaRepository $metaRepository
     * @param RoleRepository $roleRepository
     */
    public function __construct(User $user, MetaRepository $metaRepository, RoleRepository $roleRepository)
    {
        $this->fullname = $user->getFullname();

        $this->minimumAmount = $metaRepository->getInt('audit_minimum_amount', 200);
        $this->recommendedAmount = $metaRepository->getInt('recommended_amount', $this->minimumAmount);

        $this->membershipTeamEmail = $roleRepository->findOneByName(Role::TEAM_MEMBERSHIP)->getEmail();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(config('branding.space_name') . ': Thank you for your donation')
                    ->markdown('emails.membership.membershipUnderPaid');
    }
}
