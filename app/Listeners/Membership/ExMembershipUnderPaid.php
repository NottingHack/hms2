<?php

namespace App\Listeners\Membership;

use App\Events\Banking\ExMemberPaymentUnderMinimum;
use App\Mail\Membership\MembershipExUnderPaid;
use HMS\Factories\Banking\MembershipStatusNotificationFactory;
use HMS\Repositories\Banking\MembershipStatusNotificationRepository;
use HMS\Repositories\MetaRepository;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\UserRepository;
use HMS\User\Permissions\RoleManager;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class ExMembershipUnderPaid implements ShouldQueue
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
     *  @var RoleRepository
     */
    protected $roleRepository;

    /**
     * Create the event listener.
     *
     * @param UserRepository                         $userRepository
     * @param RoleManager                            $roleManager
     * @param MembershipStatusNotificationFactory    $membershipStatusNotificationFactory
     * @param MembershipStatusNotificationRepository $membershipStatusNotificationRepository
     * @param MetaRepository                         $metaRepository
     * @param RoleRepository                         $roleRepository
     */
    public function __construct(
        UserRepository $userRepository,
        RoleManager $roleManager,
        MembershipStatusNotificationFactory $membershipStatusNotificationFactory,
        MembershipStatusNotificationRepository $membershipStatusNotificationRepository,
        MetaRepository $metaRepository,
        RoleRepository $roleRepository
    ) {
        $this->userRepository = $userRepository;
        $this->roleManager = $roleManager;
        $this->membershipStatusNotificationFactory = $membershipStatusNotificationFactory;
        $this->membershipStatusNotificationRepository = $membershipStatusNotificationRepository;
        $this->metaRepository = $metaRepository;
        $this->roleRepository = $roleRepository;
    }

    /**
     * Handle the event.
     *
     * @param ExMemberPaymentUnderMinimum $event
     *
     * @return void
     */
    public function handle(ExMemberPaymentUnderMinimum $event)
    {
        // get a fresh copy of the user
        $user = $this->userRepository->findOneById($event->user->getId());

        // File an already cleared MembershipStatusNotification against the bankTransaction
        $membershipStatusNotification = $this->membershipStatusNotificationFactory
            ->createForUnderPayment($user, $user->getAccount())
            ->clearNotificationsByRevoke();
        $this->membershipStatusNotificationRepository->save($membershipStatusNotification);

        // email user
        Mail::to($user)->send(new MembershipExUnderPaid($user, $this->metaRepository, $this->roleRepository));
    }
}
