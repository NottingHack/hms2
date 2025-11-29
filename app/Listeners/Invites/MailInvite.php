<?php

namespace App\Listeners\Invites;

use App\Events\MembershipInterestRegistered;
use App\Mail\InterestRegistered;
use HMS\Repositories\MetaRepository;
use HMS\Repositories\RoleRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class MailInvite implements ShouldQueue
{
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
     * @return void
     */
    public function __construct(MetaRepository $metaRepository, RoleRepository $roleRepository)
    {
        $this->metaRepository = $metaRepository;
        $this->roleRepository = $roleRepository;
    }

    /**
     * Handle the event.
     *
     * @param MembershipInterestRegistered $event
     *
     * @return void
     */
    public function handle(MembershipInterestRegistered $event)
    {
        Mail::to($event->invite->getEmail())
            ->queue(new InterestRegistered($event->invite, $this->metaRepository, $this->roleRepository));
    }
}
