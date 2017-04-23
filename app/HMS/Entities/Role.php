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
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $permissions;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $users;

    /**
     * @var string Team email address
     */
    protected $email;

    /**
     * @var string Team slack channel
     */
    protected $slackChannel;

    /**
     * @var boolean Should this role be retained by ex members
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * @param string $displayName
     * @return self
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return self
     */
    public function setDescription($description)
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

    public function addPermission(Permission $permission)
    {
        if ( ! $this->permissions->contains($permission)) {
            $this->permissions->add($permission);
        }
    }

    public function removePermission(Permission $permission)
    {
        $this->permissions->removeElement($permission);
    }

    public function stripPermissions()
    {
        $this->permissions->clear();
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
    public function getEmail()
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
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Gets the value of slackChannel.
     *
     * @return string team slack channel
     */
    public function getSlackChannel()
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
    public function setSlackChannel($slackChannel)
    {
        $this->slackChannel = $slackChannel;

        return $this;
    }

    /**
     * Gets the value of retained.
     *
     * @return boolean Should this role be retained by ex members
     */
    public function getRetained()
    {
        return $this->retained;
    }

    /**
     * Sets the value of retained.
     *
     * @param boolean Should this role be retained by ex members $retained the retained
     *
     * @return self
     */
    public function setRetained($retained)
    {
        $this->retained = $retained;

        return $this;
    }

    /**
     * Route notifications for the Slack channel.
     *
     * @return string
     */
    public function routeNotificationForSlack()
    {
        if ($this->name = 'team.Trustees') {
            return Meta::get('trustee_slack_webhook');
        } else {
            return Meta::get('team_slack_webhook');
        }
    }
}
