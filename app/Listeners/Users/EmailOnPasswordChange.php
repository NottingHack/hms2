<?php

namespace App\Listeners\Users;

use App\Events\Users\UserPasswordChanged;
use App\Notifications\Users\PasswordChanged;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailOnPasswordChange implements ShouldQueue
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
     * @param UserPasswordChanged $event
     *
     * @return void
     */
    public function handle(UserPasswordChanged $event)
    {
        $event->user->notify(new PasswordChanged());
    }
}
