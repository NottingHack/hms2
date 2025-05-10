<?php

namespace App\Listeners\Governance;

use App\Events\Roles\UserAddedToRole;
use App\Events\Roles\UserRemovedFromRole;
use Carbon\Carbon;
use HMS\Entities\Role;
use HMS\Factories\Governance\RegisterOfMembersFactory;
use HMS\Repositories\Governance\RegisterOfMembersRepository;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\UserRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Dispatcher;
use Illuminate\Queue\InteractsWithQueue;

class RegisterOfMembersLogger implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct(
        protected RegisterOfMembersFactory $registerOfMembersFactory,
        protected RegisterOfMembersRepository $registerOfMembersRepository,
        protected UserRepository $userRepository,
        protected RoleRepository $roleRepository,
    ) {
    }

    /**
     * Handle user added to role events.
     */
    public function onUserAddedToRole(UserAddedToRole $event): void
    {
        if ($event->role->getName() != Role::MEMBER_CURRENT) {
            return;
        }

        $user = $this->userRepository->findOneById($event->user->getId());

        $_registerOfMember = $this->registerOfMembersFactory->create(
            $user,
            $user->getFirstname(),
            $user->getLastname(),
            Carbon::now(),
        );

        $this->registerOfMembersRepository->save($_registerOfMember);
    }

    /**
     * Handle user removed from role events.
     */
    public function onUserRemovedFromRole(UserRemovedFromRole $event): void
    {
        if ($event->role->getName() != Role::MEMBER_CURRENT) {
            return;
        }

        $user = $this->userRepository->findOneById($event->user->getId());

        $registerOfMember = $this->registerOfMembersRepository->findCurrentByUser($user);

        if (is_null($registerOfMember)) {
            $this->fail('Failed to find current Register Of Member for ' . $user->getFullname());
        }

        $registerOfMember->setEndedAt(Carbon::now());

        $registerOfMembersRepository->save($registerOfMember);
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
