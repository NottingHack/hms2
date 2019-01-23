<?php

namespace App\Events;

use HMS\Entities\Invite;
use Illuminate\Queue\SerializesModels;

class MembershipInterestRegistered
{
    use SerializesModels;

    /**
     * @var Invite
     */
    public $invite;

    /**
     * Create a new event instance.
     *
     * @param Invite $invite
     *
     * @return void
     */
    public function __construct(Invite $invite)
    {
        $this->invite = $invite;
    }
}
