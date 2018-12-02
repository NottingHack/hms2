<?php

namespace App\Listeners\Users;

use Illuminate\Queue\InteractsWithQueue;
use App\Events\Users\UserPasswordChanged;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Users\PasswordChanged;

class EmailOnPasswordChange
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
     * @param  object  $event
     * @return void
     */
    public function handle(UserPasswordChanged $event)
    {
        $event->user->notify(new PasswordChanged());

    }
}
