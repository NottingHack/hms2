<?php

namespace App\Events\Roles;

use HMS\Entities\Role;
use HMS\Entities\User;
use Illuminate\Queue\SerializesModels;

class UserAddedToRole
{
    use SerializesModels;

    /**
     * @var User
     */
    public $user;

    /**
     * @var Role
     */
    public $role;

    /**
     * @var User|null
     */
    public $updateBy;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, Role $role)
    {
        $this->user = $user;
        $this->role = $role;
        $this->updateBy = \Auth::user();
    }
}
