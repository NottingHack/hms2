<?php

namespace App\Events\Governance;

use HMS\Entities\Governance\Proxy;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

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
