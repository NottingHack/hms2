<?php

namespace App\Mail\Teams;

use Carbon\Carbon;
use HMS\Repositories\RoleRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TeamReminder extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var string
     */
    public $teamName;

    /**
     * Create a new message instance.
     *
     * @param string $teamName
     *
     * @return void
     */
    public function __construct(string $teamName)
    {
        $this->teamName = $teamName;
    }

    /**
     * Build the message.
     *
     * @param RoleRepository $roleRepository
     *
     * @return $this
     */
    public function build(RoleRepository $roleRepository)
    {
        $trusteesTeamRole = $roleRepository->findOneByName(Role::TEAM_TRUSTEES);
        $trusteesEmail = $trusteesTeamRole->getEmail();
        $trusteesMgEmail = preg_replace('/@/m', '@mg.', $trusteesEmail);

        $month = Carbon::now()->addDays(8)->format('F');

        return $this->from($trusteesMgEmail, $trusteesTeamRole->getDisplayName())
            ->replyTo($trusteesEmail, $trusteesTeamRole->getDisplayName())
            ->subject('Team Reminder: ' . $month . ' Members Meeting Update')
            ->markdown('emails.teams.reminder');
    }
}
