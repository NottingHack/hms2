<?php

namespace App\Listeners\Membership;

use HMS\Entities\Role;
use HMS\Repositories\RoleRepository;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\NewMemberApprovalNeeded;

class ApprovalEmailOnUserRegistered implements ShouldQueue
{
    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * Create the event listener.
     *
     * @param  RoleRepository $RoleRepository
     */
    public function __construct(roleRepository $roleRepository)
    {

        $this->roleRepository= $roleRepository;
    }

    /**
     * Handle the event.
     *
     * @param  Registered  $event
     * @return void
     */
    public function handle(Registered $event)
    {
        $membershipTeamRole = $this->roleRepository->findByName(Role::TEAM_MEMBERSHIP);
        $membershipTeamRole->notify(new NewMemberApprovalNeeded($event->user));
    }
}
