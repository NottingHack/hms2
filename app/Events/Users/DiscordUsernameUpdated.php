<?php

namespace App\Events\Users;

use HMS\Entities\Profile;
use HMS\Entities\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class DiscordUsernameUpdated
{
    use SerializesModels;

    /**
     * @var User
     */
    public $user;

    /**
     * @var Profile
     */
    public $profile;

    /**
     * @var User|null
     */
    public $updateBy;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, Profile $profile)
    {
        $this->user = $user;
        $this->profile = $profile;
        $this->updateBy = Auth::user();
    }
}
