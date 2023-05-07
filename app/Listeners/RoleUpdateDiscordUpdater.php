<?php

namespace App\Listeners;

use App\Events\Roles\UserAddedToRole;
use App\Events\Roles\UserRemovedFromRole;
use App\Events\Users\DiscordUsernameUpdated;
use Doctrine\ORM\EntityManagerInterface;
use HMS\Entities\Role;
use HMS\Helpers\Discord;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\RoleUpdateRepository;
use HMS\Repositories\UserRepository;
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
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     * @param RoleRepository $roleRepository
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
        if (! config('services.discord.token')) {
            return;
        }

        $this->discord = new Discord(
            config('services.discord.token'),
            config('services.discord.guild_id')
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

        if (! $profile->getDiscordUserId()) {
            return;
        }

        $discordMember = $this->discord->findMemberByUsername($profile->getDiscordUserId());
        $discordRole = $this->discord->findRoleByName($role->getDisplayName());

        if (! $discordMember || ! $discordRole) {
            return;
        }

        $this->discord->getDiscordClient()->guild->addGuildMemberRole([
            'guild.id' => config('services.discord.guild_id'),
            'user.id' => $discordMember->user->id,
            'role.id' => $discordRole->id,
        ]);

        Log::info('RoleUpdateDiscordUpdater@onUserAddedToRole: ' .
                  $user->getUsername() . ' added to discord role ' . $role->getDisplayName());
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

        if (! $profile->getDiscordUserId()) {
            return;
        }

        $discordMember = $this->discord->findMemberByUsername($profile->getDiscordUserId());
        $discordRole = $this->discord->findRoleByName($role->getDisplayName());

        if (! $discordMember || ! $discordRole) {
            return;
        }

        $this->discord->getDiscordClient()->guild->removeGuildMemberRole([
            'guild.id' => config('services.discord.guild_id'),
            'user.id' => $discordMember->user->id,
            'role.id' => $discordRole->id,
        ]);

        Log::info('RoleUpdateDiscordUpdater@onUserRemovedFromRole: ' .
                  $user->getUsername() . ' removed from discord role ' . $role->getDisplayName());
    }

    /**
     * Handles user setting their Discord username field
     * i.e. push all current roles.
     *
     * @param DiscordUsernameUpdated $event
     */
    public function onDiscordUsernameUpdated(DiscordUsernameUpdated $event)
    {
        $user = $event->user;
        $profile = $event->profile;

        if (! $profile->getDiscordUserId()) {
            return;
        }
        $discordUserId = $profile->getDiscordUserId();
        $hmsUsername = $user->getUsername();

        $memberRole = $this->roleRepository->findMemberStatusForUser($user);
        $memberTeams = $this->roleRepository->findTeamsForUser($user);

        $discordMember = $this->discord->findMemberByUsername($discordUserId);

        if (! $discordMember) {
            return;
        }

        $message = <<<EOF
Hi **$discordUserId**.

Your Discord account has been linked to the HMS profile **$hmsUsername**. If you did not do this, contact a trustee, including the username mentioned above.

Have fun!
EOF;
        $dm = $this->discord->getDiscordClient()->user->createDm([
            'recipient_id' => $discordMember->user->id,
        ]);
        $this->discord->getDiscordClient()->channel->createMessage([
            'channel.id' => $dm->id,
            'content' => $message,
        ]);

        $discordMemberRole = $this->discord->findRoleByName($memberRole->getDisplayName());
        if ($discordMemberRole) {
            $this->discord->getDiscordClient()->guild->addGuildMemberRole([
                'guild.id' => config('services.discord.guild_id'),
                'user.id' => $discordMember->user->id,
                'role.id' => $discordMemberRole->id,
            ]);
            Log::info('RoleUpdateDiscordUpdater@onDiscordUsernameUpdated: ' .
                      $user->getUsername() . ' added to discord role ' . $memberRole->getDisplayName());
        }

        foreach ($memberTeams as $team) {
            $discordTeamRole = $this->discord->findRoleByName($team->getDisplayName());
            if (! $discordTeamRole) {
                continue;
            }

            $this->discord->getDiscordClient()->guild->addGuildMemberRole([
                'guild.id' => config('services.discord.guild_id'),
                'user.id' => $discordMember->user->id,
                'role.id' => $discordTeamRole->id,
            ]);
            Log::info('RoleUpdateDiscordUpdater@onDiscordUsernameUpdated: ' .
                      $user->getUsername() . ' added to discord role ' . $team->getDisplayName());
        }
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param Illuminate\Events\Dispatcher $events
     */
    public function subscribe($events)
    {
        if (! config('services.discord.token')) {
            return;
        }

        $events->listen(
            'App\Events\Roles\UserAddedToRole',
            'App\Listeners\RoleUpdateDiscordUpdater@onUserAddedToRole'
        );

        $events->listen(
            'App\Events\Roles\UserRemovedFromRole',
            'App\Listeners\RoleUpdateDiscordUpdater@onUserRemovedFromRole'
        );

        $events->listen(
            'App\Events\Users\DiscordUsernameUpdated',
            'App\Listeners\RoleUpdateDiscordUpdater@onDiscordUsernameUpdated'
        );
    }
}

// asjackson#5316
