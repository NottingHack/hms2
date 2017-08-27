<?php

namespace App\Listeners\Membership;

use HMS\Repositories\MetaRepository;
use HMS\Repositories\UserRepository;
use HMS\User\Permissions\RoleManager;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Mail\Membership\MembershipMayBeRevoked;
use App\Events\Banking\MembershipPaymentWarning;
use HMS\Factories\Banking\MembershipStatusNotificationFactory;
use HMS\Repositories\Banking\MembershipStatusNotificationRepository;

class WarnMembershipMayExpire implements ShouldQueue
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
     * @var MembershipStatusNotificationFactory
     */
    protected $membershipStatusNotificationFactory;

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
        MembershipStatusNotificationFactory $membershipStatusNotificationFactory,
        MembershipStatusNotificationRepository $membershipStatusNotificationRepository,
        MetaRepository $metaRepository)
    {
        $this->userRepository = $userRepository;
        $this->roleManager = $roleManager;
        $this->membershipStatusNotificationFactory = $membershipStatusNotificationFactory;
        $this->membershipStatusNotificationRepository = $membershipStatusNotificationRepository;
        $this->metaRepository = $metaRepository;
    }

    /**
     * Handle the event.
     *
     * @param  MembershipPaymentWarning  $event
     * @return void
     */
    public function handle(MembershipPaymentWarning $event)
    {
        // get a fresh copy of the user
        $user = $this->userRepository->find($event->user->getId());

        $membershipStatusNotification = $this->membershipStatusNotificationFactory->create($user, $user->getAccount());
        $this->membershipStatusNotificationRepository->save($membershipStatusNotification);

        // email user
        \Mail::to($user)->send(new MembershipMayBeRevoked($user, $this->metaRepository));
    }
}
