<?php

namespace App\Listeners\Membership;

use App\Events\Banking\NonPaymentOfMinimumMembership;
use App\Mail\Membership\MembershipRevokedDueToUnderPayment;
use HMS\Entities\Role;
use HMS\Repositories\Banking\BankRepository;
use HMS\Repositories\Banking\MembershipStatusNotificationRepository;
use HMS\Repositories\Members\BoxRepository;
use HMS\Repositories\MetaRepository;
use HMS\Repositories\UserRepository;
use HMS\User\Permissions\RoleManager;
use Illuminate\Contracts\Queue\ShouldQueue;

class RevokeMembershipUnderPaid implements ShouldQueue
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
     * @var BankRepository
     */
    protected $bankRepository;

    /**
     *  @var BoxRepository
     */
    protected $boxRepository;

    /**
     * Create the event listener.
     *
     * @param UserRepository $userRepository
     * @param RoleManager $roleManager
     * @param MembershipStatusNotificationRepository $membershipStatusNotificationRepository
     * @param MetaRepository $metaRepository
     * @param BankRepository $bankRepository
     * @param BoxRepository $boxRepository
     */
    public function __construct(
        UserRepository $userRepository,
        RoleManager $roleManager,
        MembershipStatusNotificationRepository $membershipStatusNotificationRepository,
        MetaRepository $metaRepository,
        BankRepository $bankRepository,
        BoxRepository $boxRepository
    ) {
        $this->userRepository = $userRepository;
        $this->roleManager = $roleManager;
        $this->membershipStatusNotificationRepository = $membershipStatusNotificationRepository;
        $this->metaRepository = $metaRepository;
        $this->bankRepository = $bankRepository;
        $this->boxRepository = $boxRepository;
    }

    /**
     * Handle the event.
     *
     * @param NonPaymentOfMinimumMembership $event
     *
     * @return void
     */
    public function handle(NonPaymentOfMinimumMembership $event)
    {
        // get a fresh copy of the user
        $user = $this->userRepository->findOneById($event->user->getId());

        if (! $user->hasRoleByName([Role::MEMBER_CURRENT, Role::MEMBER_YOUNG])) {
            // should not be here
            // TODO: tell some one about it
            return;
        }

        // remove all non retained roles (this will include MEMBER_CURRENT and MEMBER_YOUNG)
        foreach ($user->getRoles() as $role) {
            if (! $role->getRetained()) {
                $this->roleManager->removeUserFromRole($user, $role);
            }
        }

        // make ex member
        $this->roleManager->addUserToRoleByName($user, Role::MEMBER_EX);

        // clear their notifications
        $userNotifications = $this->membershipStatusNotificationRepository->findOutstandingNotificationsByUser($user);
        foreach ($userNotifications as $notification) {
            $notification->clearNotificationsByRevoke();
            $this->membershipStatusNotificationRepository->save($notification);
        }

        // email user
        \Mail::to($user)->send(
            new MembershipRevokedDueToUnderPayment(
                $user,
                $this->metaRepository,
                $this->bankRepository,
                $this->boxRepository
            )
        );
    }
}
