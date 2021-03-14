<?php

namespace App\Events\Roles;

use HMS\Entities\Role;
use HMS\Entities\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class UserRemovedFromRole
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
     *  @param User $user
     *  @param Role $role
     */
    public function __construct(User $user, Role $role)
    {
        $this->user = $user;
        $this->role = $role;
        $this->updateBy = Auth::user();
    }
}
