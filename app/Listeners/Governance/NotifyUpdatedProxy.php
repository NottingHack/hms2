<?php

namespace App\Listeners\Governance;

use App\Events\Governance\ProxyUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Governance\Proxy\PrincipalAccepted;
use App\Notifications\Governance\Proxy\PrincipalCancelled;
use App\Notifications\Governance\Proxy\ProxyUpdated as ProxyUpdatedNotification;

class NotifyUpdatedProxy implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ProxyUpdated  $event
     * @return void
     */
    public function handle(ProxyUpdated $event)
    {
        $proxy = $event->proxy;
        $oldProxy = $event->oldProxy;

        // Notify Old Proxy
        $oldProxy->notify(new PrincipalCancelled($proxy->getMeeting(), $proxy->getPrincipal()));
        // Notify Proxy
        $proxy->getProxy()->notify(new PrincipalAccepted($proxy));
        // Notify Principal
        $proxy->getPrincipal()->notify(new ProxyUpdatedNotification($proxy, $oldProxy));
    }
}
