<?php

namespace App\Events\Banking;

use Illuminate\Queue\SerializesModels;

class AuditRequest
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
