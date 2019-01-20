<?php

namespace App\Listeners\Membership;

use Carbon\Carbon;
use HMS\Entities\Role;
use HMS\Repositories\MetaRepository;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\UserRepository;
use HMS\User\Permissions\RoleManager;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Mail\Membership\MembershipReinstated;
use App\Events\Banking\ReinstatementOfMembershipPayment;

class ReinstateMembership implements ShouldQueue
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var RoleManager
     */
    protected $roleManager;
    protected $metaRepository;
    protected $roleRepository;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(UserRepository $userRepository,
        RoleManager $roleManager,
        MetaRepository $metaRepository,
        RoleRepository $roleRepository)
    {
        $this->userRepository = $userRepository;
        $this->roleManager = $roleManager;
        $this->metaRepository = $metaRepository;
        $this->roleRepository = $roleRepository;
    }

    /**
     * Handle the event.
     *
     * @param  ReinstatementOfMembershipPayment  $event
     * @return void
     */
    public function handle(ReinstatementOfMembershipPayment $event)
    {
        // get a fresh copy of the user
        $user = $this->userRepository->findOneById($event->user->getId());

        // update roles
        if (! $user->hasRoleByName(Role::MEMBER_EX)) {
            // we shouldn not be here get out
            // TODO: email some one about it
            return;
        }

        $dob = $user->getProfile()->getDateOfBirth();
        if (is_null($dob)) {
            // no dob assume old enough
            $this->roleManager->addUserToRoleByName($user, Role::MEMBER_CURRENT);
        } elseif ($dob->diffInYears(Carbon::now()) >= 18) { //TODO: meta constants
            $this->roleManager->addUserToRoleByName($user, Role::MEMBER_CURRENT);
        } elseif ($dob->diffInYears(Carbon::now()) >= 16) {
            $this->roleManager->addUserToRoleByName($user, Role::MEMBER_YOUNG);
        } else {
            // should not be here to young
            // TODO: email some one about it
            return;
        }

        $this->roleManager->removeUserFromRoleByName($user, Role::MEMBER_EX);

        // emial user
        \Mail::to($user)->send(new MembershipReinstated($user, $this->metaRepository, $this->roleRepository));
    }
}
