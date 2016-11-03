<?php

namespace HMS\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use LaravelDoctrine\ACL\Contracts\Permission;
use LaravelDoctrine\ACL\Contracts\Role as RoleContract;
use LaravelDoctrine\ACL\Mappings as ACL;
use LaravelDoctrine\ACL\Permissions\HasPermissions;

class Role implements RoleContract
{
    use HasPermissions;

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
     * @var string Name of Permission
     */
    protected $name;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $permissions;

    /**
     * Role constructor.
     * @param $name
     */
    public function __construct($name)
    {
        $this->name = $name;
        $this->permissions = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
        if (!$this->permissions->contains($permission)) {
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
}
