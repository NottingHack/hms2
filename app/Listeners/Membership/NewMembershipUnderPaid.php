<?php

namespace App\Listeners\Membership;

use App\Events\Banking\NewMembershipPaidUnderMinimum;
use App\Mail\Membership\MembershipUnderPaid;
use HMS\Repositories\MetaRepository;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\UserRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class NewMembershipUnderPaid implements ShouldQueue
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

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
     * @param UserRepository $userRepository
     * @param RoleRepository $roleRepository
     * @param MetaRepository $metaRepository
     *
     * @return void
     */
    public function __construct(
        UserRepository $userRepository,
        MetaRepository $metaRepository,
        RoleRepository $roleRepository,
    ) {
        $this->userRepository = $userRepository;
        $this->metaRepository = $metaRepository;
        $this->roleRepository = $roleRepository;
    }

    /**
     * Handle the event.
     *
     * @param NewMembershipPaidUnderMinimum $event
     *
     * @return void
     */
    public function handle(NewMembershipPaidUnderMinimum $event)
    {
        // get a fresh copy of the user
        $user = $this->userRepository->findOneById($event->user->getId());

        // email user
        Mail::to($user)->send(
            new MembershipUnderPaid($user, $this->metaRepository, $this->roleRepository)
        );
    }
}
