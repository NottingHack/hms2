<?php

namespace App\Events\Roles;

use HMS\Entities\Role;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RoleCreated
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var HMS\Entities\Role
     */
    public $role;

    /**
     * Create a new event instance.
     *
     * @param HMS\Entities\Role $role
     */
    public function __construct(Role $role)
    {
        //
        $this->role = $role;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
