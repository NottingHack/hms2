<?php

namespace HMS\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use HMS\Helpers\Discord;
use HMS\Traits\Entities\SoftDeletable;
use HMS\Traits\Entities\Timestampable;
use Illuminate\Support\Facades\Crypt;
use LaravelDoctrine\ACL\Contracts\Permission;
use LaravelDoctrine\ACL\Contracts\Role as RoleContract;
use LaravelDoctrine\ACL\Permissions\HasPermissions;
use LaravelDoctrine\ORM\Notifications\Notifiable;

class Role implements RoleContract
{
    use HasPermissions, SoftDeletable, Timestampable, Notifiable;

    /*
     * Member roles names.
     */
    public const MEMBER_CURRENT = 'member.current';
    public const MEMBER_APPROVAL = 'member.approval';
    public const MEMBER_PAYMENT = 'member.payment';
    public const MEMBER_YOUNG = 'member.young';
    public const MEMBER_EX = 'member.ex';
    public const MEMBER_TEMPORARYBANNED = 'member.temporarybanned';
    public const MEMBER_BANNED = 'member.banned';

    /*
     * Member roles.
     */
    public const MEMBER_ROLES = [
        self::MEMBER_CURRENT,
        self::MEMBER_APPROVAL,
        self::MEMBER_PAYMENT,
        self::MEMBER_YOUNG,
        self::MEMBER_EX,
        self::MEMBER_TEMPORARYBANNED,
        self::MEMBER_BANNED,
    ];

    /**
     * Role names.
     */
    public const SUPERUSER = 'user.super';
    public const TEMPORARY_ACCESS = 'user.temporaryAccess';
    public const BUILDING_ACCESS = 'user.buildingAccess';
    public const TEMPORARY_VIEW_REGISTER_OF_MEMBERS = 'user.temporaryViewRegisterOfMembers';
    public const TEMPORARY_VIEW_REGISTER_OF_DIRECTORS = 'user.temporaryViewRegisterOfDirectors';
    public const TEAM_MEMBERSHIP = 'team.membership';
    public const TEAM_TRUSTEES = 'team.trustees';
    public const TEAM_SOFTWARE = 'team.software';
    public const TEAM_FINANCE = 'team.finance';

    /**
     * Length of the trimmed description.
     */
    public const TRIMMED_LENGHT = 100;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string Name of Role
     */
    protected $name;

    /**
     * @var string Display Name of the Role
     */
    protected $displayName;

    /**
     * @var string Description of the Role
     */
    protected $description;

    /**
     * @var \Doctrine\Common\Collections\Collection|Permission[]
     */
    protected $permissions;

    /**
     * @var \Doctrine\Common\Collections\Collection|User[]
     */
    protected $users;

    /**
     * @var null|string Team email address
     */
    protected $email;

    /**
     * @var null|string Encrypted password for email address
     */
    protected $emailPassword;

    /**
     * @var bool Should we maintain email forwarding aliases
     */
    protected $emailSyncForwarding;

    /**
     * @var null|string Team slack channel
     */
    protected $slackChannel;

    /**
     * @var null|string Public Discord channel for the team
     */
    protected $discordChannel;

    /**
     * @var null|string Private Discord channel (if needed) for the team
     */
    protected $discordPrivateChannel;

    /**
     * @var bool Should this role be retained by ex members
     */
    protected $retained;

    /**
     * Role constructor.
     *
     * @param $name
     * @param $displayName
     * @param $description
     */
    public function __construct($name, $displayName, $description)
    {
        $this->name = $name;
        $this->displayName = $displayName;
        $this->description = $description;
        $this->permissions = new ArrayCollection();
        $this->retained = false;
        $this->emailSyncForwarding = false;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * @param string $displayName
     *
     * @return self
     */
    public function setDisplayName($displayName): self
    {
        $this->displayName = $displayName;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getDescriptionTrimed()
    {
        if (strlen($this->description) > self::TRIMMED_LENGHT) {
            return substr($this->description, 0, self::TRIMMED_LENGHT) . '...';
        }

        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return self
     */
    public function setDescription($description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection|Permission[]
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * Add a Permission to the Role.
     *
     * @param Permission $permission
     *
     * @return self
     */
    public function addPermission(Permission $permission): self
    {
        if (! $this->permissions->contains($permission)) {
            $this->permissions->add($permission);
        }

        return $this;
    }

    /**
     * Remove a single permission from the Role.
     *
     * @param Permission $permission
     *
     * @return self
     */
    public function removePermission(Permission $permission): self
    {
        $this->permissions->removeElement($permission);

        return $this;
    }

    /**
     * Remove all permissions from the Role.
     *
     * @return self
     */
    public function stripPermissions(): self
    {
        $this->permissions->clear();

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection|User[]
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Gets the value of email.
     *
     * @return string Team email address
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Sets the value of email.
     *
     * @param string Team email address $email the email
     *
     * @return self
     */
    public function setEmail($email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return null|string Encrypted password for email address
     */
    public function getEmailPassword(bool $decrypt = false): ?string
    {
        if ($decrypt && ! is_null($this->emailPassword)) {
            return Crypt::decryptString($this->emailPassword);
        }

        return $this->emailPassword;
    }

    /**
     * @param null|string Encrypted password for email address $emailPassword
     *
     * @return self
     */
    public function setEmailPassword($emailPassword): self
    {
        $this->emailPassword = Crypt::encryptString($emailPassword);

        return $this;
    }

    /**
     * @return bool Should we maintain email forwarding aliases
     */
    public function isEmailSyncForwarding()
    {
        return $this->emailSyncForwarding;
    }

    /**
     * @param bool Should we maintain email forwarding aliases $emailSyncForwarding
     *
     * @return self
     */
    public function setEmailSyncForwarding($emailSyncForwarding)
    {
        $this->emailSyncForwarding = $emailSyncForwarding;

        return $this;
    }

    /**
     * Gets the value of slackChannel.
     *
     * @return string team slack channel
     */
    public function getSlackChannel(): ?string
    {
        return $this->slackChannel;
    }

    /**
     * Sets the value of slackChannel.
     *
     * @param string team slack channel $slackChannel the slack channel
     *
     * @return self
     */
    public function setSlackChannel($slackChannel): self
    {
        $this->slackChannel = $slackChannel;

        return $this;
    }

    /**
     * Gets the value of discordChannel.
     *
     * @return string team's discord channel
     */
    public function getDiscordChannel(): ?string
    {
        return $this->discordChannel;
    }

    /**
     * Sets the value of slackChannel.
     *
     * @param string team's discord channel
     *
     * @return self
     */
    public function setDiscordChannel($discordChannel): self
    {
        $this->discordChannel = $discordChannel;

        return $this;
    }

    /**
     * Gets the value of discordPrivateChannel.
     *
     * @return string team's private discord channel
     */
    public function getDiscordPrivateChannel(): ?string
    {
        return $this->discordPrivateChannel;
    }

    /**
     * Sets the value of discordPrivateChannel.
     *
     * @param string team's private discord channel
     *
     * @return self
     */
    public function setDiscordPrivateChannel($discordPrivateChannel): self
    {
        $this->discordPrivateChannel = $discordPrivateChannel;

        return $this;
    }

    /**
     * Gets the value of retained.
     *
     * @return bool Should this role be retained by ex members
     */
    public function getRetained(): bool
    {
        return $this->retained;
    }

    /**
     * Sets the value of retained.
     *
     * @param bool Should this role be retained by ex members $retained the retained
     *
     * @return self
     */
    public function setRetained($retained): self
    {
        $this->retained = $retained;

        return $this;
    }

    /**
     * Route notifications for the Slack channel.
     *
     * @return false|string
     */
    public function routeNotificationForSlack(): false|string
    {
        if (! config('hms.features.slack')) {
            return false;
        }

        if ($this->name == self::TEAM_TRUSTEES) {
            return config('hms.trustees_slack_webhook', false);
        } else {
            return config('hms.team_slack_webhook', false);
        }
    }

    /**
     * Route notifications to the Discord channel.
     *
     * @return null|string
     */
    public function routeNotificationForDiscord(): ?string
    {
        if (! config('services.discord.token')) {
            return null;
        }

        $discord = new Discord(
            config('services.discord.token'),
            config('services.discord.guild_id')
        );

        if ($this->name == self::TEAM_TRUSTEES) {
            // Trustee discord role has access to membership private channel.
            // Returning null to avoid duplicate message on membership audit.
            return null;
        } else {
            if ($this->getDiscordPrivateChannel()) {
                return $discord->findChannelByName($this->getDiscordPrivateChannel())['id'];
            } else {
                return $discord->findChannelByName($this->getDiscordChannel())['id'];
            }
        }
    }

    /**
     * Get Category protion of the role name.
     *
     * @return string
     */
    public function getCategory()
    {
        [$category, $name] = explode('.', $this->name);

        return $category;
    }
}
