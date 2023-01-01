<?php

namespace App\Listeners\Membership;

use App\Events\Banking\NewMembershipPaidUnderMinimum;
use App\Mail\Membership\MembershipUnderPaid;
use HMS\Repositories\MetaRepository;
use HMS\Repositories\UserRepository;
use Illuminate\Contracts\Queue\ShouldQueue;

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
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(
        UserRepository $userRepository,
        MetaRepository $metaRepository
    ) {
        $this->userRepository = $userRepository;
        $this->metaRepository = $metaRepository;
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
        \Mail::to($user)->send(new MembershipUnderPaid($user, $this->metaRepository));
    }
}
