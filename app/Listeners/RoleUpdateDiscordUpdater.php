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
     * Check that the role is suitable for being updated on Discord.
     *
     * @param string $roleName
     *
     * @return bool
     */
    private function checkRoleName($roleName)
    {
        return str_starts_with($roleName, 'team.') ||
               $roleName == Role::MEMBER_CURRENT ||
               $roleName == Role::MEMBER_YOUNG;
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

        if (! $this->checkRoleName($role->getName())) {
            return;
        }

        if (! $profile->getDiscordUsername()) {
            return;
        }

        $discordMember = $this->discord->findMemberByProfile($profile);
        $discordRole = $this->discord->findRoleByName($role->getDisplayName());

        if (! $discordMember || ! $discordRole) {
            return;
        }

        $this->discord->getDiscordClient()->guild->addGuildMemberRole([
            'guild.id' => config('services.discord.guild_id'),
            'user.id' => (int) $discordMember['user']['id'],
            'role.id' => (int) $discordRole['id'],
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

        if (! $this->checkRoleName($role->getName())) {
            return;
        }

        if (! $profile->getDiscordUsername()) {
            return;
        }

        $discordMember = $this->discord->findMemberByProfile($profile);
        $discordRole = $this->discord->findRoleByName($role->getDisplayName());

        if (! $discordMember || ! $discordRole) {
            return;
        }

        $this->discord->getDiscordClient()->guild->removeGuildMemberRole([
            'guild.id' => config('services.discord.guild_id'),
            'user.id' => (int) $discordMember['user']['id'],
            'role.id' => (int) $discordRole['id'],
        ]);

        Log::info('RoleUpdateDiscordUpdater@onUserRemovedFromRole: ' .
                  $user->getUsername() . ' removed from discord role ' . $role->getDisplayName());
    }

    /**
     * Removes roles from a Discord member, trying their snowflake first, then username.
     *
     * @param Profile the HMS profile
     */
    private function cleanupOldDiscordUserRoles($profile)
    {
        $discordMember = $this->discord->findMemberByProfile($profile);

        // Give up
        if (! $discordMember) {
            return;
        }

        foreach ($discordMember['roles'] as $discordRoleId) {
            $this->discord->getDiscordClient()->guild->removeGuildMemberRole([
                'guild.id' => config('services.discord.guild_id'),
                'user.id' => (int) $discordMember['user']['id'],
                'role.id' => (int) $discordRoleId,
            ]);
        }
    }

    /**
     * Handles user setting their Discord username field
     * i.e. push all current roles.
     *
     * @param DiscordUsernameUpdated $event
     */
    public function onDiscordUsernameUpdated(DiscordUsernameUpdated $event)
    {
        $user = $this->userRepository->findOneById($event->user->getId());
        $profile = $user->getProfile();
        $oldDiscordUsername = $event->oldDiscordUsername;

        $memberRole = $this->roleRepository->findMemberStatusForUser($user);
        $memberTeams = $this->roleRepository->findTeamsForUser($user);

        if ($oldDiscordUsername) {
            $this->cleanupOldDiscordUserRoles($profile);
            $profile->setDiscordUserSnowflake(null);
            $this->userRepository->save($user);
        }

        if (! $this->checkRoleName($memberRole->getName())) {
            return;
        }

        if (! $profile->getDiscordUsername()) {
            return;
        }

        $hmsUsername = $user->getUsername();

        $discordMember = $this->discord->findMemberByProfile($profile);

        if (! $discordMember) {
            return;
        }

        $profile->setDiscordUserSnowflake($discordMember['user']['id']);
        $this->userRepository->save($user);

        $discordMemberRole = $this->discord->findRoleByName($memberRole->getDisplayName());
        if ($discordMemberRole) {
            $this->discord->getDiscordClient()->guild->addGuildMemberRole([
                'guild.id' => config('services.discord.guild_id'),
                'user.id' => (int) $discordMember['user']['id'],
                'role.id' => (int) $discordMemberRole['id'],
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
                'user.id' => (int) $discordMember['user']['id'],
                'role.id' => (int) $discordTeamRole['id'],
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
