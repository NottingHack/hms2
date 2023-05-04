<?php

namespace HMS\Helpers;

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

        foreach ($this->roles as $r) {
            if ($r->name == $name) {
                return $r;
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
        if (! $this->members) {
            // This supports pagination in chunks of 1000 by setting the
            // 'after' parameter to the last member user id. I don't think
            // we need to worry about this yet.
            $this->members = $this->client->guild->listGuildMembers([
                'guild.id' => $this->guildId,
                'limit' => 1000,
            ]);
        }

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

            foreach ($this->members as $m) {
                if ($m->user->username == $userPart ||
                    $m->user->discriminator == $discrPart) {
                    return $m;
                }
            }
        } else {
            foreach ($this->members as $m) {
                if ($m->user->username == $username) {
                    return $m;
                }
            }
        }

        return null;
    }
}
