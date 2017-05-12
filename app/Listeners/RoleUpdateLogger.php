<?php

namespace App\Listeners;

use HMS\Entities\Role;
use HMS\Entities\RoleUpdate;
use App\Events\Roles\UserAddedToRole;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\Roles\UserRemovedFromRole;
use HMS\Repositories\RoleUpdateRepository;
use Illuminate\Contracts\Queue\ShouldQueue;

class RoleUpdateLogger implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * @var RoleUpdateRepository
     */
    protected $roleUpdateRepository;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * Create the event listener.
     *
     * @param RoleUpdateRepository $roleUpdateRepository
     */
    public function __construct(RoleUpdateRepository $roleUpdateRepository, EntityManagerInterface $entityManager)
    {
        $this->roleUpdateRepository = $roleUpdateRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * Handle user added to role events.
     *
     * @param  UserAddedToRole $event
     */
    public function onUserAddedToRole(UserAddedToRole $event)
    {
        if ($event->role->getName() == Role::MEMBER_APPROVAL) {
            return;
        }

        $user = $this->entityManager->merge($event->user);
        $role = $this->entityManager->merge($event->role);
        $roleUpdate = new RoleUpdate($user, $role);
        $this->roleUpdateRepository->save($roleUpdate);
    }

    /**
     * Handle user removed from role events.
     *
     * @param  UserRemovedFromRole $event
     */
    public function onUserRemovedFromRole(UserRemovedFromRole $event)
    {
        $user = $this->entityManager->merge($event->user);
        $role = $this->entityManager->merge($event->role);
        $roleUpdate = new RoleUpdate($user, null, $role);
        $this->roleUpdateRepository->save($roleUpdate);
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen(
            'App\Events\Roles\UserAddedToRole',
            'App\Listeners\RoleUpdateLogger@onUserAddedToRole'
        );

        $events->listen(
            'App\Events\Roles\UserRemovedFromRole',
            'App\Listeners\RoleUpdateLogger@onUserRemovedFromRole'
        );
    }
}
