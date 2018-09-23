<?php

namespace HMS\Entities;

use HMS\Traits\Entities\SoftDeletable;
use HMS\Traits\Entities\Timestampable;
use Illuminate\Notifications\Notifiable;
use LaravelDoctrine\ACL\Contracts\Permission;
use Doctrine\Common\Collections\ArrayCollection;
use LaravelDoctrine\ACL\Permissions\HasPermissions;
use LaravelDoctrine\ACL\Contracts\Role as RoleContract;

class Role implements RoleContract
{
    use HasPermissions, SoftDeletable, Timestampable, Notifiable;

    const MEMBER_CURRENT = 'member.current';
    const MEMBER_APPROVAL = 'member.approval';
    const MEMBER_PAYMENT = 'member.payment';
    const MEMBER_YOUNG = 'member.young';
    const MEMBER_EX = 'member.ex';

    const SUPERUSER = 'user.super';

    const TEAM_MEMBERSHIP = 'team.membership';
    const TEAM_TRUSTEES = 'team.trustees';
    const TEAM_SOFTWARE = 'team.software';
    const TEAM_FINANCE = 'team.finance';

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
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $permissions;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $users;

    /**
     * @var null|string Team email address
     */
    protected $email;

    /**
     * @var null|string Team slack channel
     */
    protected $slackChannel;

    /**
     * @var bool Should this role be retained by ex members
     */
    protected $retained;

    /**
     * Role constructor.
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
     * @param string $description
     * @return self
     */
    public function setDescription($description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return ArrayCollection|Permission[]
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * Add a Permission to the Role.
     *
     * @param Permission $permission
     * @return self
     */
    public function addPermission(Permission $permission): self
    {
        if ( ! $this->permissions->contains($permission)) {
            $this->permissions->add($permission);
        }

        return $this;
    }

    /**
     * Remove a single permission from the Role.
     * @param Permission $permission
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
     * @return ArrayCollection|User[]
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
     * @return null|string
     */
    public function routeNotificationForSlack(): ?string
    {
        if ($this->name == self::TEAM_TRUSTEES) {
            return config('hms.trustee_slack_webhook');
        } else {
            return config('hms.team_slack_webhook');
        }
    }
}
