<?php

namespace App\Listeners\Membership;

use HMS\Repositories\MetaRepository;
use HMS\Repositories\UserRepository;
use HMS\User\Permissions\RoleManager;
use HMS\Repositories\Members\BoxRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use HMS\Repositories\Banking\BankRepository;
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
     * @var BankRepository
     */
    protected $bankRepository;

    /**
     * @var BoxRepository
     */
    protected $boxRepository;

    /**
     * Create the event listener.
     *
     * @param UserRepository                         $userRepository
     * @param RoleManager                            $roleManager
     * @param MembershipStatusNotificationFactory    $membershipStatusNotificationFactory
     * @param MembershipStatusNotificationRepository $membershipStatusNotificationRepository
     * @param MetaRepository                         $metaRepository
     * @param BankRepository                         $bankRepository
     * @param BoxRepository                          $boxRepository
     */
    public function __construct(
        UserRepository $userRepository,
        RoleManager $roleManager,
        MembershipStatusNotificationFactory $membershipStatusNotificationFactory,
        MembershipStatusNotificationRepository $membershipStatusNotificationRepository,
        MetaRepository $metaRepository,
        BankRepository $bankRepository,
        BoxRepository $boxRepository
    ) {
        $this->userRepository = $userRepository;
        $this->roleManager = $roleManager;
        $this->membershipStatusNotificationFactory = $membershipStatusNotificationFactory;
        $this->membershipStatusNotificationRepository = $membershipStatusNotificationRepository;
        $this->metaRepository = $metaRepository;
        $this->bankRepository = $bankRepository;
        $this->boxRepository = $boxRepository;
    }

    /**
     * Handle the event.
     *
     * @param MembershipPaymentWarning $event
     *
     * @return void
     */
    public function handle(MembershipPaymentWarning $event)
    {
        // get a fresh copy of the user
        $user = $this->userRepository->findOneById($event->user->getId());

        $membershipStatusNotification = $this->membershipStatusNotificationFactory->create($user, $user->getAccount());
        $this->membershipStatusNotificationRepository->save($membershipStatusNotification);

        // email user
        \Mail::to($user)->send(new MembershipMayBeRevoked($user, $this->metaRepository, $this->bankRepository, $this->boxRepository));
    }
}
