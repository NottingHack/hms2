<?php

namespace App\Events\Governance;

use HMS\Entities\Governance\Proxy;
use HMS\Entities\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProxyUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Proxy
     */
    public $proxy;

    /**
     * @var User
     */
    public $oldProxy;

    /**
     * Create a new event instance.
     *
     * @param Proxy $proxy
     * @param User $oldProxy
     *
     * @return void
     */
    public function __construct(Proxy $proxy, User $oldProxy)
    {
        $this->proxy = $proxy;
        $this->oldProxy = $oldProxy;
    }
}
