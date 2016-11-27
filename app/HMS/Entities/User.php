<?php

namespace HMS\Entities;

use HMS\Traits\Entities\SoftDeletable;
use Doctrine\Common\Collections\ArrayCollection;
use HMS\Traits\Entities\Timestampable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use LaravelDoctrine\ACL\Contracts\HasPermissions as HasPermissionsContract;
use LaravelDoctrine\ACL\Contracts\HasRoles as HasRoleContract;
use Illuminate\Auth\Passwords\CanResetPassword;
use LaravelDoctrine\ACL\Permissions\HasPermissions;
use LaravelDoctrine\ACL\Roles\HasRoles;
use LaravelDoctrine\ORM\Notifications\Notifiable;

class User implements AuthenticatableContract, CanResetPasswordContract, HasRoleContract, HasPermissionsContract
{
    use CanResetPassword, Notifiable, HasRoles, HasPermissions, SoftDeletable, Timestampable;

    const MIN_PASSWORD_LENGTH = 3;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string Users name
     */
    protected $name;

    /**
     * @var string Users username for login
     */
    protected $username;

    /**
     * @var string Users email address
     */
    protected $email;

    /**
     * @var string Users remember me token for persisting login sessions
     */
    protected $rememberToken;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection|\LaravelDoctrine\ACL\Contracts\Role[]
     */
    protected $roles;

    /**
     * User constructor.
     * @param $name
     * @param $username
     * @param $email
     */
    public function __construct($name, $username, $email)
    {
        $this->name = $name;
        $this->username = $username;
        $this->email = $email;
        $this->roles = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'username';
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->{$this->getAuthIdentifierName()};
    }

    /**
     * Get the password for the user.
     * @return string
     */
    public function getAuthPassword()
    {
        throw new Exception('Not Supported');
    }

    /**
     * Get the token value for the "remember me" session.
     * @return string
     */
    public function getRememberToken()
    {
        return $this->{$this->getRememberTokenName()};
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param string $value
     *
     * @return void
     */
    public function setRememberToken($value)
    {
        $this->{$this->getRememberTokenName()} = $value;
    }

    /**
     * Get the column name for the "remember me" token.
     * @return string
     */
    public function getRememberTokenName()
    {
        return 'rememberToken';
    }

    /**
     * @return ArrayCollection|Role[]
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @return ArrayCollection|Permission[]
     */
    public function getPermissions()
    {
        // user's don't directly have permissions, only via their roles
        return [];
    }
}
