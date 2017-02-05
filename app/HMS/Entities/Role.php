<?php

namespace HMS\Entities;

use HMS\Traits\Entities\SoftDeletable;
use HMS\Traits\Entities\Timestampable;
use LaravelDoctrine\ACL\Contracts\Permission;
use Doctrine\Common\Collections\ArrayCollection;
use LaravelDoctrine\ACL\Permissions\HasPermissions;
use LaravelDoctrine\ACL\Contracts\Role as RoleContract;

class Role implements RoleContract
{
    use HasPermissions, SoftDeletable, Timestampable;

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
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
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
     */
    public function setDescription($description)
    {
        $this->description = $description;
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
}
