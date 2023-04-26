<?php

namespace App\Listeners;

use App\Events\Roles\UserAddedToRole;
use App\Events\Roles\UserRemovedFromRole;
use Doctrine\ORM\EntityManagerInterface;
use HMS\Entities\Role;
use HMS\Entities\Profile;
use HMS\Entities\RoleUpdate;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\RoleUpdateRepository;
use HMS\Repositories\UserRepository;
use HMS\Helpers\Discord;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class RoleUpdateDiscordUpdater implements ShouldQueue
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
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * @var Discord
     */
    protected $discord;

    /**
     * Create the event listener.
     *
     * @param RoleUpdateRepository $roleUpdateRepository
     */
    public function __construct(
        RoleUpdateRepository $roleUpdateRepository,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        RoleRepository $roleRepository
    ) {
        $this->roleUpdateRepository = $roleUpdateRepository;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;

        // If the token isn't set, we can give up now.
        if (! env('DISCORD_TOKEN', null)) return;

        $this->discord = new Discord(
            env('DISCORD_TOKEN'), (int)env('DISCORD_GUILD_ID')
        );
    }

    /**
     * Handle user added to role events.
     *
     * @param UserAddedToRole $event
     */
    public function onUserAddedToRole(UserAddedToRole $event)
    {
        $user = $this->userRepository->findOneById($event->user->getId());
        $role = $this->roleRepository->findOneById($event->role->getId());
        $profile = $user->getProfile();

        if (! $profile->getDiscordUserId()) return;

        $discordMember = $this->discord->findMemberByUsername($profile->getDiscordUserId());
        $discordRole = $this->discord->findRoleByName($role->getDisplayName());

        if (! $discordMember || ! $discordRole) return;

        $this->discord->getDiscordClient()->guild->addGuildMemberRole([
            'guild.id' => (int)env('DISCORD_GUILD_ID'),
            'user.id' => $discordMember->user->id,
            'role.id' => $discordRole->id
        ]);
    }

    /**
     * Handle user removed from role events.
     *
     * @param UserRemovedFromRole $event
     */
    public function onUserRemovedFromRole(UserRemovedFromRole $event)
    {
        $user = $this->userRepository->findOneById($event->user->getId());
        $role = $this->roleRepository->findOneById($event->role->getId());
        $profile = $user->getProfile();

        if (! $profile->getDiscordUserId()) return;

        $discordMember = $this->discord->findMemberByUsername($profile->getDiscordUserId());
        $discordRole = $this->discord->findRoleByName($role->getDisplayName());

        if (! $discordMember || ! $discordRole) return;

        $this->discord->getDiscordClient()->guild->removeGuildMemberRole([
            'guild.id' => (int)env('DISCORD_GUILD_ID'),
            'user.id' => $discordMember->user->id,
            'role.id' => $discordRole->id
        ]);
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param Illuminate\Events\Dispatcher $events
     */
    public function subscribe($events)
    {
        if (! env('DISCORD_TOKEN', null)) return;

        $events->listen(
            'App\Events\Roles\UserAddedToRole',
            'App\Listeners\RoleUpdateDiscordUpdater@onUserAddedToRole'
        );

        $events->listen(
            'App\Events\Roles\UserRemovedFromRole',
            'App\Listeners\RoleUpdateDiscordUpdater@onUserRemovedFromRole'
        );
    }
}
