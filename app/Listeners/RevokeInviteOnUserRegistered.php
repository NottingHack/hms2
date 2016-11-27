<?php

namespace App\Listeners;

use HMS\Repositories\InviteRepository;
use Illuminate\Auth\Events\Registered;
use Doctrine\ORM\EntityManagerInterface;

class RevokeInviteOnUserRegistered
{
    /**
     * @var HMS\Repositories\InviteRepository
     */
    protected $inviteRepository;

    /**
     * @var Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(InviteRepository $inviteRepository, EntityManagerInterface $em)
    {
        //
        $this->inviteRepository = $inviteRepository;
        $this->em = $em;
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

        $this->em->remove($invite);
        $this->em->flush();
    }
}
