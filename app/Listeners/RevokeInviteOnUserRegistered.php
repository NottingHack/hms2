<?php

namespace App\Listeners;

use HMS\Repositories\InviteRepository;
use Illuminate\Auth\Events\Registered;
use Doctrine\ORM\EntityManagerInterface;

class RevokeInviteOnUserRegistered
{
    /**
     * @var InviteRepository
     */
    protected $inviteRepository;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * Create the event listener.
     *
     * @param  InviteRepository $inviteRepository
     * @param  EntityManagerInterface $em
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
