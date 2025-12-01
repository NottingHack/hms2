<?php

namespace App\Mail;

use HMS\Entities\Invite;
use HMS\Entities\Role;
use HMS\Repositories\MetaRepository;
use HMS\Repositories\RoleRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InterestRegistered extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var string
     */
    public $token;

    /**
     * @var string
     */
    public $membershipEmail;

    /**
     * @var string
     */
    public $trusteesEmail;

    /**
     * @var string
     */
    public $groupLink;

    /**
     * @var string
     */
    public $rulesLink;

    /**
     * @var string
     */
    public $discordHTML;

    /**
     * Create a new message instance.
     *
     * @param Invite $invite
     * @param MetaRepository $metaRepository
     * @param RoleRepository $roleRepository
     */
    public function __construct(
        Invite $invite,
        MetaRepository $metaRepository,
        RoleRepository $roleRepository
    ) {
        $this->token = $invite->getInviteToken();
        $this->membershipEmail = $roleRepository->findOneByName(Role::TEAM_MEMBERSHIP)->getEmail();
        $this->trusteesEmail = $roleRepository->findOneByName(Role::TEAM_TRUSTEES)->getEmail();
        $this->groupLink = $metaRepository->get('google_group_html');
        $this->rulesLink = $metaRepository->get('rules_html');
        $this->discordHTML = $metaRepository->get('discord_html');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(config('branding.space_name') . ': Interest registered')
                    ->markdown('emails.interestRegistered');
    }
}
