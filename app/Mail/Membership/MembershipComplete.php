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
     * @return void
     */
    public function __construct(User $user, MetaRepository $metaRepository, RoleRepository $roleRepository)
    {
        $this->fullname = $user->getFullname();

        $this->membersGuideHTML = $metaRepository->get('link_Members Guide');
        $this->membersGuidePDF = $metaRepository->get('members_guide_pdf');
        $this->outerDoorCode = $metaRepository->get('access_street_door');
        $this->innerDoorCode = $metaRepository->get('access_inner_door');
        $this->wifiSsid = $metaRepository->get('access_wifi_ssid');
        $this->wifiPass = $metaRepository->get('access_wifi_password');
        $this->groupLink = $metaRepository->get('link_Google Group');
        $this->rulesHTML = $metaRepository->get('link_Hackspace Rules');
        $this->wikiLink = $metaRepository->get('link_Wiki');

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
