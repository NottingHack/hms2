<?php

namespace App\Listeners\Membership;

use Carbon\Carbon;
use HMS\Entities\Role;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\UserRepository;
use HMS\User\Permissions\RoleManager;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Mail\Membership\EighteenCongratulations;
use App\Events\Membership\YoungHackerAuditRequest;
use App\Notifications\Membership\YoungHackerTurnedEighteen;

class AuditYoungHackers implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * @var RoleManager
     */
    protected $roleManager;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * Create the event listener.
     *
     * @param RoleRepository $roleRepository
     * @param RoleManager $roleManager
     * @param UserRepository $userRepository
     */
    public function __construct(
        RoleRepository $roleRepository,
        RoleManager $roleManager,
        UserRepository $userRepository
    ) {
        $this->roleRepository = $roleRepository;
        $this->roleManager = $roleManager;
        $this->userRepository = $userRepository;
    }

    /**
     * Handle the event.
     *
     * @param YoungHackerAuditRequest $event
     */
    public function handle(YoungHackerAuditRequest $event)
    {
        $youngMembers = $this->roleRepository->findOneByName(Role::MEMBER_YOUNG)->getUsers();

        foreach ($youngMembers as $user) {
            $dob = $user->getProfile()->getDateOfBirth();
            if (is_null($dob)) {
                // should not be here
                // TODO: email someone about it
                continue;
            } elseif ($dob->diffInYears(Carbon::now()) >= 18) { //TODO: meta constants
                $this->roleManager->addUserToRoleByName($user, Role::MEMBER_CURRENT);
                $this->roleManager->removeUserFromRoleByName($user, Role::MEMBER_YOUNG);

                // email  user
                \Mail::to($user)->send(new EighteenCongratulations($user));

                // now email the audit results
                $youngHackerTurnedEighteenNotification = new YoungHackerTurnedEighteen($user);

                $membershipTeamRole = $this->roleRepository->findOneByName(Role::TEAM_MEMBERSHIP);
                // TODO: decide if we should really send this?
                // $membershipTeamRole->notify($youngHackerTurnedEighteenNotification);

                $trusteesTeamRole = $this->roleRepository->findOneByName(Role::TEAM_TRUSTEES);
                $trusteesTeamRole->notify($youngHackerTurnedEighteenNotification);
            }
        }
    }
}
