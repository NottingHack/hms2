<?php

namespace App\Events\Roles;

use HMS\Entities\Role;
use HMS\Entities\User;
use Illuminate\Queue\SerializesModels;

class UserAddedToRole
{
    use SerializesModels;

    /**
     * @var HMS\Entities\User
     */
    public $user;

    /**
     * @var HMS\Entities\Role
     */
    public $role;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, Role $role)
    {
        //
        $this->user = $user;
        $this->role = $role;
    }
}
