<?php

namespace App\Listeners\Governance;

use App\Events\Roles\UserAddedToRole;
use App\Events\Roles\UserRemovedFromRole;
use HMS\Entities\Role;
use HMS\Factories\Governance\RegisterOfDirectorsFactory;
use HMS\Repositories\Governance\RegisterOfDirectorsRepository;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\UserRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Dispatcher;
use Illuminate\Queue\InteractsWithQueue;

class RegisterOfDirectorsLogger implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct(
        protected RegisterOfDirectorsFactory $registerOfDirectorsFactory,
        protected RegisterOfDirectorsRepository $registerOfDirectorsRepository,
        protected UserRepository $userRepository,
        protected RoleRepository $roleRepository,
    ) {
    }

    /**
     * Handle user added to role events.
     */
    public function onUserAddedToRole(UserAddedToRole $event): void
    {
        if ($event->role->getName() != Role::TEAM_TRUSTEES) {
            return;
        }

        $user = $this->userRepository->findOneById($event->user->getId());

        $_registerOfDirector = $this->registerOfDirectorsFactory->create(
            $user,
            $user->getFirstname(),
            $user->getLastname(),
            $user->getProfile()->getAddress1(),
            $user->getProfile()->getAddress2(),
            $user->getProfile()->getAddress3(),
            $user->getProfile()->getAddressCity(),
            $user->getProfile()->getAddressCounty(),
            $user->getProfile()->getAddressPostcode(),
            Carbon::now(),
        );

        $this->registerOfDirectorsRepository->save($_registerOfDirector);
    }

    /**
     * Handle user removed from role events.
     */
    public function onUserRemovedFromRole(UserRemovedFromRole $event): void
    {
        if ($event->role->getName() != Role::TEAM_TRUSTEES) {
            return;
        }

        $user = $this->userRepository->findOneById($event->user->getId());

        $registerOfDirector = $this->registerOfDirectorsRepository->findCurrentByUser($user);

        if (is_null($registerOfDirector)) {
            $this->fail('Failed to find current Register Of Director for ' . $user->getFullname());
        }

        $registerOfDirector->setEndedAt(Carbon::now());

        $registerOfDirectorsRepository->save($registerOfDirector);
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @return array<string, string>
     */
    public function subscribe(Dispatcher $events): array
    {
        return [
            UserAddedToRole::class => 'onUserAddedToRole',
            UserRemovedFromRole::class => 'onUserRemovedFromRole',
        ];
    }
}
