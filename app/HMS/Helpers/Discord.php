<?php

namespace HMS\Helpers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use RestCord\DiscordClient;

class Discord
{
    /**
     * @var RestCord\DiscordClient
     */
    protected $client;

    /**
     * @var RestCord\Member[]
     */
    protected $members;

    /**
     * @var RestCord\Channel[]
     */
    protected $channels;

    /**
     * @var RestCord\Role[]
     */
    protected $roles;

    /**
     * @var int
     */
    protected $guildId;

    /**
     * Creates an instance of the Discord helper.
     *
     * @param string $token  The Discord token.
     * @param int $guildId   The Discord server guild ID.
     */
    public function __construct(string $token, int $guildId)
    {
        $this->guildId = $guildId;

        $this->client = new DiscordClient([
            'token' => $token,
            'logger' => Log::getLogger(),
        ]);
    }

    /**
     * Get the Discord client object for performing operations directly.
     *
     * @return DiscordClient
     */
    public function getDiscordClient()
    {
        return $this->client;
    }

    /**
     * Find a Discord role by its name.
     *
     * @var string  The role name
     *
     * @return null|DiscordRole
     */
    public function findRoleByName(string $name)
    {
        if (! $this->roles) {
            $this->roles = $this->client->guild->getGuildRoles([
                'guild.id' => $this->guildId,
            ]);
        }

        foreach ($this->roles as $role) {
            if ($role['name'] == $name) {
                return $role;
            }
        }

        return null;
    }

    /**
     * Find a Discord member by their username.
     *
     * @var string  The full username (with discriminator)
     *
     * @return null|DiscordMember
     */
    public function findMemberByUsername(string $username)
    {
        $this->listGuildMembers(); // populate cache

        // Evolving Usernames on Discord
        // https://discord.com/blog/usernames
        // https://support-dev.discord.com/hc/en-us/articles/13667755828631
        //
        // They decided to remove the discriminator component and just
        // have a single username - this can be removed once they've
        // completed that change
        if (str_contains($username, '#')) {
            // A discord username is made up of the user's chosen username,
            // and a randomly generated discriminator. The discriminator
            // is a four digit number and the two are stuck together with
            // a # character.
            $parts = explode('#', $username);
            $userPart = $parts[0];
            $discrPart = (int) $parts[1];

            foreach ($this->members as $member) {
                if ($member['user']['username'] == $userPart ||
                    $member['user']['discriminator'] == $discrPart) {
                    return $member;
                }
            }
        } else {
            foreach ($this->members as $member) {
                if ($member['user']['username'] == $username) {
                    return $member;
                }
            }
        }

        return null;
    }

    /**
     * Get a member by their snowflake.
     *
     * @param string  Discord user snowflake
     *
     * @return null|DiscordMember
     */
    public function findMemberBySnowflake(string $snowflake)
    {
        $this->listGuildMembers(); // populate cache

        foreach ($this->members as $member) {
            if ($member['user']['id'] == $snowflake) {
                return $member;
            }
        }

        return null;
    }

    /**
     * Find a Discord member from a HMS Profile.
     *
     * @param Profile A HMS User Profile object
     *
     * @return null|DiscordMember
     */
    public function findMemberByProfile($profile)
    {
        if ($profile->getDiscordUserSnowflake()) {
            return $this->findMemberBySnowflake($profile->getDiscordUserSnowflake());
        }

        if ($profile->getDiscordUsername()) {
            return $this->findMemberByUsername($profile->getDiscordUsername());
        }

        return null;
    }

    /**
     * Return a list of all guild members.
     *
     * @return array
     */
    public function listGuildMembers()
    {
        if (! $this->members) {
            // This supports pagination in chunks of 1000 by setting the
            // 'after' parameter to the last member user id. I don't think
            // we need to worry about this yet.
            $this->members = $this->client->guild->listGuildMembers([
                'guild.id' => $this->guildId,
                'limit' => 1000,
            ]);
        }

        return $this->members;
    }

    /**
     * Obtains an array of Discord channels
     *
     * @return array
     */
    private function getChannels() {
        // Avoid hitting redis if already in used for this instance of Discord
        if ($this->channels) {
            return $this->channels;
        }

        $this->channels = Cache::remember('discord.channels', 3600, fn () => $this->client->guild->getGuildChannels([
            'guild.id' => $this->guildId
        ]));

        return $this->channels;
    }

    /**
     * Find a discord channel by name.
     *
     * @param string $channelName
     *
     * @return null|DiscordChannel
     */
    public function findChannelByName(string $channelName)
    {
        foreach ($this->getChannels() as $channel) {
            if ($channel['name'] == $channelName) {
                return $channel;
            }
        }

        return null;
    }
}
