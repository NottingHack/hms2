<?php

namespace App\Jobs\Membership;

use App\Mail\Membership\EighteenCongratulations;
use App\Notifications\Membership\YoungHackerTurnedEighteen;
use Carbon\Carbon;
use HMS\Entities\Role;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\UserRepository;
use HMS\User\Permissions\RoleManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class AuditYoungHackersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @param RoleRepository $roleRepository
     * @param RoleManager $roleManager
     * @param UserRepository $userRepository
     *
     * @return void
     */
    public function handle(
        RoleRepository $roleRepository,
        RoleManager $roleManager,
        UserRepository $userRepository
    ) {
        $youngMembers = $roleRepository->findOneByName(Role::MEMBER_YOUNG)->getUsers();

        foreach ($youngMembers as $user) {
            $dob = $user->getProfile()->getDateOfBirth();
            if (is_null($dob)) {
                // should not be here
                // TODO: email someone about it
                continue;
            } elseif ($dob->diffInYears(Carbon::now()) >= 18) { //TODO: meta constants
                $roleManager->addUserToRoleByName($user, Role::MEMBER_CURRENT);
                $roleManager->removeUserFromRoleByName($user, Role::MEMBER_YOUNG);

                // email  user
                Mail::to($user)->send(new EighteenCongratulations($user));

                // now email the audit results
                $youngHackerTurnedEighteenNotification = new YoungHackerTurnedEighteen($user);

                $membershipTeamRole = $roleRepository->findOneByName(Role::TEAM_MEMBERSHIP);
                // TODO: decide if we should really send this?
                // $membershipTeamRole->notify($youngHackerTurnedEighteenNotification);

                $trusteesTeamRole = $roleRepository->findOneByName(Role::TEAM_TRUSTEES);
                $trusteesTeamRole->notify($youngHackerTurnedEighteenNotification);
            }
        }
    }
}
