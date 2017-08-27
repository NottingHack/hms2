<?php

namespace App\Events\Banking;

use HMS\Entities\User;
use Illuminate\Queue\SerializesModels;

class MembershipPaymentWarning
{
    use SerializesModels;

    /**
     * @var User
     */
    public $user;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
