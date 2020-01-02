<?php

namespace App\Listeners\Governance;

use HMS\Entities\Role;
use HMS\Repositories\RoleRepository;
use App\Events\Governance\ProxyRegistered;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Governance\Proxy\ProxyAccepted;
use App\Notifications\Governance\Proxy\PrincipalAccepted;
use App\Notifications\Governance\Proxy\NotifyTrusteesProxyRegistered;

class NotifyNewProxyRegistered implements ShouldQueue
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
     * @param  ProxyRegistered  $event
     * @return void
     */
    public function handle(ProxyRegistered $event)
    {
        $proxy = $event->proxy;
        // Notify Proxy
        $proxy->getProxy()->notify(new PrincipalAccepted($proxy));
        // Notify Principal
        $proxy->getPrincipal()->notify(new ProxyAccepted($proxy));
        // Notify Trustees
        $trusteesTeamRole = $this->roleRepository->findOneByName(Role::TEAM_TRUSTEES);
        $trusteesTeamRole->notify(new NotifyTrusteesProxyRegistered($proxy));
    }
}
