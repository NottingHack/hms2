<?php

namespace App\Events\Users;

use HMS\Entities\User;
use Illuminate\Queue\SerializesModels;

class UserPasswordChanged
{
    use SerializesModels;

    /**
     * @var User
     */
    public $user;

    /**
     * Create a new event instance.
     *
     * @param User   $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
