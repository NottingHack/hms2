<?php

namespace App\Events\Membership;

use Illuminate\Queue\SerializesModels;

class YoungHackerAuditRequest
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
}
