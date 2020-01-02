<?php

namespace App\Events\Governance;

use HMS\Entities\User;
use HMS\Entities\Governance\Meeting;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class ProxyCheckedIn
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Meeting
     */
    public $meeting;

    /**
     * @var User
     */
    public $principal;

    /**
     * @var User
     */
    public $proxy;

    /**
     * Create a new event instance.
     *
     * @param Meeting $meeting
     * @param User $principal
     * @param User $proxy
     *
     * @return void
     */
    public function __construct(Meeting $meeting, User $principal, User $proxy)
    {
        $this->meeting = $meeting;
        $this->principal = $principal;
        $this->proxy = $proxy;
    }
}
