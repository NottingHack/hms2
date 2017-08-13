<?php

namespace App\Listeners\Membership;

use HMS\Entities\Role;
use HMS\Repositories\MetaRepository;
use HMS\Repositories\UserRepository;
use HMS\User\Permissions\RoleManager;
use App\Mail\Membership\MembershipRevoked;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\Banking\NonPaymentOfMembership;
use HMS\Repositories\Banking\MembershipStatusNotificationRepository;

class RevokeMembership implements ShouldQueue
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
     * @var MembershipStatusNotificationRepository
     */
    protected $membershipStatusNotificationRepository;

    /**
     * @var MetaRepository
     */
    protected $metaRepository;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(UserRepository $userRepository,
        RoleManager $roleManager,
        MembershipStatusNotificationRepository $membershipStatusNotificationRepository,
        MetaRepository $metaRepository)
    {
        $this->userRepository = $userRepository;
        $this->roleManager = $roleManager;
        $this->membershipStatusNotificationRepository = $membershipStatusNotificationRepository;
        $this->metaRepository = $metaRepository;
    }

    /**
     * Handle the event.
     *
     * @param  NonPaymentOfMembership  $event
     * @return void
     */
    public function handle(NonPaymentOfMembership $event)
    {
        // get a fresh copy of the user
        $user = $this->userRepository->find($event->user->getId());

        // remove current roles
        if ($user->hasRoleByName(Role::MEMBER_CURRENT)) {
            $this->roleManager->RemoveUserFromRoleByName($user, Role::MEMBER_CURRENT);
        } elseif ($user->hasRoleByName(Role::MEMBER_YOUNG)) {
            $this->roleManager->RemoveUserFromRoleByName($user, Role::MEMBER_YOUNG);
        } else {
            // should not be here
            // TODO: tell some one about it
            return;
        }

        // make ex emmebr
        $this->roleManager->addUserToRoleByName($user, Role::MEMBER_EX);

        // clear there notifications
        $userNotifications = $this->membershipStatusNotificationRepository->findOutstandingNotificationsByUser($user);
        foreach ($userNotifications as $notification) {
            $notification->clearNotificationsByRevoke();
            $this->membershipStatusNotificationRepository->save($notification);
        }

        // emial user
        \Mail::to($user)->send(new MembershipRevoked($user, $this->metaRepository));
    }
}
