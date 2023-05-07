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
    public $oldDiscordUserId;

    /**
     * @var User|null
     */
    public $updateBy;

    /**
     * Create a new event instance.
     *
     * @param User $user
     * @param Profile $profile
     * @param string|null $oldDiscordUserId
     *
     * @return void
     */
    public function __construct(User $user, Profile $profile, ?string $oldDiscordUserId)
    {
        $this->user = $user;
        $this->profile = $profile;
        $this->oldDiscordUserId = $oldDiscordUserId;
        $this->updateBy = Auth::user();
    }
}
