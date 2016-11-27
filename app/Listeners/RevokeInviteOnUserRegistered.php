<?php

namespace App\Listeners;

use HMS\Repositories\InviteRepository;
use Illuminate\Auth\Events\Registered;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class RevokeInviteOnUserRegistered
{
    /**
     * @var HMS\Repositories\InviteRepository
     */
    protected $inviteRepository;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(InviteRepository $inviteRepository)
    {
        //
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

        \EntityManager::remove($invite);
        \EntityManager::flush();
    }
}
