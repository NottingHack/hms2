<?php

namespace App\Mail\Membership;

use HMS\Entities\Role;
use HMS\Entities\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use HMS\Repositories\MetaRepository;
use HMS\Repositories\RoleRepository;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MembershipComplete extends Mailable implements ShouldQueue
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
    public $wikiLink;

    /**
     * @var string
     */
    public $membershipTeamEmail;

    /**
     * Create a new message instance.
     *
     * @param User           $user
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
        return $this->subject('Nottingham Hackspace: Membership Complete')
                    ->markdown('emails.membership.membershipComplete');
    }
}
