<?php

namespace App\Listeners\Governance;

use HMS\Entities\Role;
use HMS\Repositories\RoleRepository;
use App\Events\Governance\ProxyCancelled;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Governance\Proxy\PrincipalCancelled;
use App\Notifications\Governance\Proxy\NotifyTrusteesProxyCancelled;
use App\Notifications\Governance\Proxy\ProxyCancelled as ProxyCancelledNotification;

class NotifyCancelledProxy implements ShouldQueue
{
    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * Create the event listener.
     *
     * @param RoleRepository $roleRepository
     *
     * @return void
     */
    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * Handle the event.
     *
     * @param  ProxyCancelled  $event
     * @return void
     */
    public function handle(ProxyCancelled $event)
    {
        $meeting = $event->meeting;
        $principal = $event->principal;
        $proxy = $event->proxy;
        // Notify Proxy
        $proxy->notify(new PrincipalCancelled($meeting, $principal));
        // Notify Principal
        $principal->notify(new ProxyCancelledNotification($meeting, $proxy));
        // Notify Trustees
        $trusteesTeamRole = $this->roleRepository->findOneByName(Role::TEAM_TRUSTEES);
        $trusteesTeamRole->notify(new NotifyTrusteesProxyCancelled($meeting));
    }
}
