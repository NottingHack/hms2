<?php

namespace App\Listeners;

use App\Events\DovecotPushReceived;
use App\Notifications\NotifyIncommingRoleEmail;
use HMS\Repositories\RoleRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DovecotPushListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     *
     * @param RoleRepository $roleRepository
     */
    public function __construct(
        protected RoleRepository $roleRepository,
    ) {
        //
    }

    /**
     * Handle the event.
     *
     * @param DovecotPushReceived $event
     */
    public function handle(DovecotPushReceived $event): void
    {
        $role = $this->roleRepository->findOneByEmail($event->user);

        if (is_null($role)) {
            return;
        }

        $role->notify(new NotifyIncommingRoleEmail(
            $role,
            $event->folder,
            $event->event,
            $event->from,
            $event->subject,
            $event->snippet,
            $event->messages,
            $event->unseen,
        ));
    }
}
