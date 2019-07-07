<?php

namespace App\Events\Users;

use HMS\Entities\User;
use Illuminate\Queue\SerializesModels;

class UserEmailChanged
{
    use SerializesModels;

    /**
     * @var User
     */
    public $user;

    /**
     * @var string
     */
    public $oldEmail;

    /**
     * Create a new event instance.
     *
     * @param User $user
     * @param string $oldEmail
     */
    public function __construct(User $user, string $oldEmail)
    {
        $this->user = $user;
        $this->oldEmail = $oldEmail;
    }
}
