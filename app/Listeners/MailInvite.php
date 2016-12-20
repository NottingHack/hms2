<?php

namespace App\Listeners;

use App\Mail\InterestRegistered;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\MembershipInterestRegistered;

class MailInvite implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  MembershipInterestRegistered  $event
     * @return void
     */
    public function handle(MembershipInterestRegistered $event)
    {
        \Mail::to($event->invite->getEmail())
            ->queue(new InterestRegistered($event->invite));
    }
}
