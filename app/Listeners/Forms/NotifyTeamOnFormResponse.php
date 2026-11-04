<?php

namespace App\Listeners\Forms;

use App\Events\Forms\FormResponseReceived;
use App\Notifications\Forms\NewFormResponse;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyTeamOnFormResponse implements ShouldQueue
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
     * @param FormResponseReceived $event
     *
     * @return void
     */
    public function handle(FormResponseReceived $event)
    {
        $event->role->notify(new NewFormResponse($event->role, $event->form, $event->formResponse));
    }
}
