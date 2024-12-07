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

class MembershipReinstated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /*
    * @var string
    */
    public $fullname;

    /**
     * @var string
     */
    public $membersGuideHTML;

    /**
     * @var string
     */
    public $membersGuidePDF;

    /**
     * @var string
     */
    public $outerDoorCode;

    /**
     * @var string
     */
    public $innerDoorCode;

    /**
     * @var string
     */
    public $wifiSsid;

    /**
     * @var string
     */
    public $wifiPass;

    /**
     * @var string
     */
    public $groupLink;

    /**
     * @var string
     */
    public $rulesHTML;

    /**
     * @var string
     */
    public $slackHTML;

    /**
     * @var string
     */
    public $discordHTML;

    /**
     * @var string
     */
    public $wikiLink;

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

        $this->membersGuideHTML = $metaRepository->get('members_guide_html');
        $this->membersGuidePDF = $metaRepository->get('members_guide_pdf');
        $this->outerDoorCode = $metaRepository->get('access_street_door');
        $this->innerDoorCode = $metaRepository->get('access_inner_door');
        $this->wifiSsid = $metaRepository->get('access_wifi_ssid');
        $this->wifiPass = $metaRepository->get('access_wifi_password');
        $this->groupLink = $metaRepository->get('google_group_html');
        $this->rulesHTML = $metaRepository->get('rules_html');
        $this->slackHTML = $metaRepository->get('slack_html');
        $this->discordHTML = $metaRepository->get('discord_html');
        $this->wikiLink = $metaRepository->get('wiki_html');

        $this->membershipTeamEmail = $roleRepository->findOneByName(Role::TEAM_MEMBERSHIP)->getEmail();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(config('branding.space_name') . ': Your Membership Has Been Reinstated')
                    ->markdown('emails.membership.membershipReinstated');
    }
}
