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
     * @var string|null
     */
    public $oldDiscordUsername;

    /**
     * @var User|null
     */
    public $updateBy;

    /**
     * Create a new event instance.
     *
     * @param User $user
     * @param Profile $profile
     * @param string|null $oldDiscordUsername
     *
     * @return void
     */
    public function __construct(User $user, Profile $profile, ?string $oldDiscordUsername)
    {
        $this->user = $user;
        $this->profile = $profile;
        $this->oldDiscordUsername = $oldDiscordUsername;
        $this->updateBy = Auth::user();
    }
}
