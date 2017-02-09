<?php

namespace App\Listeners\Invites;

use HMS\Repositories\InviteRepository;
use Illuminate\Auth\Events\Registered;

class RevokeInviteOnUserRegistered
{
    /**
     * @var InviteRepository
     */
    protected $inviteRepository;

    /**
     * Create the event listener.
     *
     * @param  InviteRepository $inviteRepository
     */
    public function __construct(InviteRepository $inviteRepository)
    {
        $this->inviteRepository = $inviteRepository;
    }

    /**
     * Handle the event.
     *
     * @param  Registered  $event
     * @return void
     */
    public function handle(Registered $event)
    {
        $invite = $this->inviteRepository->findOneByEmail($event->user->getEmail());

        if (is_null($invite)) {
            return;
        }

        $this->inviteRepository->remove($invite);
    }
}
