<?php

namespace App\Events\Roles;

use HMS\Entities\Role;
use HMS\Entities\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

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
     * @var string|null
     */
    public $reason;

    /**
     * @var User|null
     */
    public $updateBy;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, Role $role, ?string $reason = null)
    {
        $this->user = $user;
        $this->role = $role;
        $this->reason = $reason;
        $this->updateBy = Auth::user();
    }
}
