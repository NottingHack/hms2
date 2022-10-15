<?php

namespace App\Listeners\Membership;

use App\Events\Banking\NewMembershipPaidFor;
use App\Mail\Membership\MembershipComplete;
use Carbon\Carbon;
use HMS\Entities\Role;
use HMS\Factories\Gatekeeper\PinFactory;
use HMS\Repositories\Gatekeeper\PinRepository;
use HMS\Repositories\MetaRepository;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\UserRepository;
use HMS\User\Permissions\RoleManager;
use Illuminate\Contracts\Queue\ShouldQueue;

class ApproveNewMembership implements ShouldQueue
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var RoleManager
     */
    protected $roleManager;

    /**
     * @var PinFactory
     */
    protected $pinFactory;

    /**
     * @var PinRepository
     */
    protected $pinRepository;

    /**
     * @var MetaRepository
     */
    protected $metaRepository;

    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(
        UserRepository $userRepository,
        RoleManager $roleManager,
        PinFactory $pinFactory,
        PinRepository $pinRepository,
        MetaRepository $metaRepository,
        RoleRepository $roleRepository
    ) {
        $this->userRepository = $userRepository;
        $this->roleManager = $roleManager;
        $this->pinFactory = $pinFactory;
        $this->pinRepository = $pinRepository;
        $this->metaRepository = $metaRepository;
        $this->roleRepository = $roleRepository;
    }

    /**
     * Handle the event.
     *
     * @param BankingNewMembershipPaidFor $event
     *
     * @return void
     */
    public function handle(NewMembershipPaidFor $event)
    {
        // get a fresh copy of the user
        $user = $this->userRepository->findOneById($event->user->getId());

        // update roles
        if (! $user->hasRoleByName(Role::MEMBER_PAYMENT)) {
            // we should not be here get out
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

        $this->roleManager->removeUserFromRoleByName($user, Role::MEMBER_PAYMENT);

        // create a pin
        if (count($this->pinRepository->findByUser($user)) == 0) {
            $pin = $this->pinFactory->createNewEnrollPinForUser($user);
            $this->pinRepository->save($pin);
            $user->pin = $pin; // ensure pin is up to date for MembershipComplete email
        }

        // update join date
        $user->getProfile()->setJoinDate(Carbon::now());
        $this->userRepository->save($user);

        // email user
        \Mail::to($user)->send(new MembershipComplete($user, $this->metaRepository, $this->roleRepository));
    }
}
