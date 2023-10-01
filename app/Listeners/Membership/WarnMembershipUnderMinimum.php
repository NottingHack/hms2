<?php

namespace App\Listeners\Membership;

use App\Events\Banking\MembershipPaymentMinimumWarning;
use App\Mail\Membership\MembershipMayBeRevokedDueToUnderPayment;
use HMS\Factories\Banking\MembershipStatusNotificationFactory;
use HMS\Repositories\Banking\BankRepository;
use HMS\Repositories\Banking\MembershipStatusNotificationRepository;
use HMS\Repositories\Members\BoxRepository;
use HMS\Repositories\MetaRepository;
use HMS\Repositories\UserRepository;
use HMS\User\Permissions\RoleManager;
use Illuminate\Contracts\Queue\ShouldQueue;

class WarnMembershipUnderMinimum implements ShouldQueue
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
     * @param MembershipPaymentMinimumWarning $event
     *
     * @return void
     */
    public function handle(MembershipPaymentMinimumWarning $event)
    {
        // get a fresh copy of the user
        $user = $this->userRepository->findOneById($event->user->getId());

        $membershipStatusNotification = $this->membershipStatusNotificationFactory
            ->createForUnderPayment($user, $user->getAccount());
        $this->membershipStatusNotificationRepository->save($membershipStatusNotification);

        // email user
        \Mail::to($user)->send(
            new MembershipMayBeRevokedDueToUnderPayment($user, $this->metaRepository, $this->bankRepository, $this->boxRepository)
        );
    }
}
