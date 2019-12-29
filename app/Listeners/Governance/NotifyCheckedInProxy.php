<?php

namespace App\Listeners\Governance;

use App\Events\Governance\ProxyCheckedIn;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Governance\Proxy\PrincipalCheckedIn;

class NotifyCheckedInProxy implements ShouldQueue
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
     * @param  ProxyCheckedIn  $event
     * @return void
     */
    public function handle(ProxyCheckedIn $event)
    {
        $meeting = $event->meeting;
        $principal = $event->principal;
        $proxy = $event->proxy;

        // Notify Proxy
        $proxy->notify(new PrincipalCheckedIn($meeting, $principal));
    }
}
