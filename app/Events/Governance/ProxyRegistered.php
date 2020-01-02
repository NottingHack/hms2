<?php

namespace App\Events\Governance;

use HMS\Entities\Governance\Proxy;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class ProxyRegistered
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Proxy
     */
    public $proxy;

    /**
     * Create a new event instance.
     *
     * @param Proxy $proxy
     *
     * @return void
     */
    public function __construct($proxy)
    {
        $this->proxy = $proxy;
    }
}
