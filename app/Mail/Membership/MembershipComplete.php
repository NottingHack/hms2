<?php

namespace App\Mail\Membership;

use HMS\Entities\Role;
use HMS\Entities\User;
use HMS\Repositories\MetaRepository;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\UserRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

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
    public $membershipPin;

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
    public $wikiLink;

    /**
     * @var string
     */
    public $membershipTeamEmail;

    /*
     * @var string
     */
    public $gatekeeperSetupGuide;

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @param MetaRepository $metaRepository
     * @param RoleRepository $roleRepository
     * @param UserRepository $userRepository
     */
    public function __construct(User $user, MetaRepository $metaRepository, RoleRepository $roleRepository, UserRepository $userRepository)
    {
        // get a fresh copy of the user
        $user = $userRepository->findOneById($user->getId());

        $this->fullname = $user->getFullname();
        $this->membershipPin = $user->getPin()->getPin();

        $this->membersGuideHTML = $metaRepository->get('members_guide_html');
        $this->membersGuidePDF = $metaRepository->get('members_guide_pdf');
        $this->outerDoorCode = $metaRepository->get('access_street_door');
        $this->innerDoorCode = $metaRepository->get('access_inner_door');
        $this->wifiSsid = $metaRepository->get('access_wifi_ssid');
        $this->wifiPass = $metaRepository->get('access_wifi_password');
        $this->groupLink = $metaRepository->get('google_group_html');
        $this->rulesHTML = $metaRepository->get('rules_html');
        $this->slackHTML = $metaRepository->get('slack_html');
        $this->wikiLink = $metaRepository->get('wiki_html');
        $this->gatekeeperSetupGuide = $metaRepository->get('gatekeeper_setup_guide');

        $this->membershipTeamEmail = $roleRepository->findOneByName(Role::TEAM_MEMBERSHIP)->getEmail();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(config('branding.space_name') . ': Membership Complete')
                    ->markdown('emails.membership.membershipComplete');
    }
}
