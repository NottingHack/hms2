<?php

namespace App\Listeners\Membership;

use App\Events\Banking\ExMemberPaymentUnderMinimum;
use App\Mail\Membership\MembershipExUnderPaid;
use HMS\Repositories\MetaRepository;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\UserRepository;
use HMS\User\Permissions\RoleManager;
use Illuminate\Contracts\Queue\ShouldQueue;

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
     * @return void
     */
    public function __construct(
        UserRepository $userRepository,
        RoleManager $roleManager,
        MetaRepository $metaRepository,
        RoleRepository $roleRepository
    ) {
        $this->userRepository = $userRepository;
        $this->roleManager = $roleManager;
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

        // email user
        \Mail::to($user)->send(new MembershipExUnderPaid($user, $this->metaRepository, $this->roleRepository));
    }
}
