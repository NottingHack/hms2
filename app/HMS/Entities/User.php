<?php

namespace HMS\Entities;

use HMS\Entities\GateKeeper\Pin;
use HMS\Entities\Banking\Account;
use Laravel\Passport\HasApiTokens;
use HMS\Entities\GateKeeper\RfidTag;
use HMS\Traits\Entities\SoftDeletable;
use HMS\Traits\Entities\Timestampable;
use LaravelDoctrine\ACL\Roles\HasRoles;
use Illuminate\Auth\Passwords\CanResetPassword;
use Doctrine\Common\Collections\ArrayCollection;
use HMS\Traits\Entities\DoctrineMustVerifyEmail;
use LaravelDoctrine\ORM\Notifications\Notifiable;
use Illuminate\Foundation\Auth\Access\Authorizable;
use LaravelDoctrine\ACL\Permissions\HasPermissions;
use LaravelDoctrine\ACL\Contracts\HasRoles as HasRoleContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use LaravelDoctrine\ACL\Contracts\HasPermissions as HasPermissionsContract;

class User implements
    AuthenticatableContract,
    CanResetPasswordContract,
    HasRoleContract,
    HasPermissionsContract,
    AuthorizableContract,
    MustVerifyEmailContract
{
    use CanResetPassword,
        Notifiable,
        HasRoles,
        HasPermissions,
        SoftDeletable,
        Timestampable,
        Authorizable,
        HasApiTokens,
        DoctrineMustVerifyEmail;

    const MIN_PASSWORD_LENGTH = 8;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string Users first name
     */
    public $name;

    /**
     * @var string Users last name
     */
    protected $lastname;

    /**
     * @var string Users username for login
     */
    protected $username;

    /**
     * @var string Users email address
     */
    public $email;

    /**
     * @var string Users remember me token for persisting login sessions
     */
    protected $rememberToken;

    /**
     * @var \Doctrine\Common\Collections\Collection|\LaravelDoctrine\ACL\Contracts\Role[]
     */
    protected $roles;

    /**
     * @var null|Profile The users profile
     */
    protected $profile;

    /**
     * @var null|Account
     */
    protected $account;

    /**
     * @var \Doctrine\Common\Collections\Collection|Email[]
     */
    protected $emails;

    /**
     * @var null|Pin
     */
    protected $pin;

    /**
     * @var ArrayCollection|RfidTag[]
     */
    protected $rfidTags;

    /**
     * @var bool
     */
    protected $google2faEnable;

    /**
     * @var string|null
     */
    protected $google2faSecret;

    /**
     * Encrypted recovery codes.
     *
     * @var string
     */
    protected $google2faRecoveryCodes;

    /**
     * User constructor.
     *
     * @param string $firstname
     * @param string $lastname
     * @param string $username
     * @param string $email
     */
    public function __construct(
        string $firstname,
        string $lastname,
        string $username,
        string $email
    ) {
        $this->name = $firstname;
        $this->lastname = $lastname;
        $this->username = $username;
        $this->email = $email;
        $this->roles = new ArrayCollection();
        $this->emails = new ArrayCollection();
        $this->rfidTags = new ArrayCollection();
        $this->google2faEnable = false;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFirstname(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getLastname(): string
    {
        return $this->lastname;
    }

    /**
     * @return string
     */
    public function getFullname(): string
    {
        return $this->name . ' ' . $this->lastname;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName(): string
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
     *
     * @return string
     */
    public function getAuthPassword()
    {
        throw new \Exception('Not Supported');
    }

    /**
     * Get the token value for the "remember me" session.
     *
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
     * @return self
     */
    public function setRememberToken($value): self
    {
        $this->{$this->getRememberTokenName()} = $value;

        return $this;
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName(): string
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

    /**
     * @return null|Profile The users profile
     */
    public function getProfile() : ?Profile
    {
        return $this->profile;
    }

    /**
     * @param null|Profile $profile
     *
     * @return self
     */
    public function setProfile(?Profile $profile): self
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * @return null|Account The users account
     */
    public function getAccount() : ?Account
    {
        return $this->account;
    }

    /**
     * @param null|Account $account
     *
     * @return self
     */
    public function setAccount(?Account $account): self
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Use by passport.
     *
     * @return int
     */
    public function getKey()
    {
        return $this->getAuthIdentifier();
    }

    /**
     * Sets the value of firstname.
     *
     * @param string $firstname the name
     *
     * @return self
     */
    public function setFirstname($firstname)
    {
        $this->name = $firstname;

        return $this;
    }

    /**
     * Sets the value of lastname.
     *
     * @param string $lastname the lastname
     *
     * @return self
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Sets the value of email.
     *
     * @param string $email the email
     *
     * @return self
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return ArrayCollection|Email[]
     */
    public function getEmails()
    {
        return $this->emails;
    }

    /**
     * @return null|HMS\Entities\GateKeeper\Pin
     */
    public function getPin()
    {
        return $this->pin;
    }

    /**
     * @param null|HMS\Entities\GateKeeper\Pin $pin
     *
     * @return self
     */
    public function setPin(Pin $pin)
    {
        $this->pin = $pin;

        return $this;
    }

    /**
     * @return RfidTag[]
     */
    public function getRfidTags()
    {
        return $this->rfidTags;
    }

    /**
     * @param ArrayCollection|RfidTag[] $rfidTags
     *
     * @return self
     */
    public function setRfidTags($rfidTags)
    {
        $this->rfidTags = $rfidTags;

        return $this;
    }

    /**
     * @return bool
     */
    public function isGoogle2faEnable()
    {
        return $this->google2faEnable;
    }

    /**
     * @param bool $google2faEnable
     *
     * @return self
     */
    public function setGoogle2faEnable(bool $google2faEnable)
    {
        $this->google2faEnable = $google2faEnable;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getGoogle2faSecret()
    {
        return $this->google2faSecret;
    }

    /**
     * @param string|null $google2faSecret
     *
     * @return self
     */
    public function setGoogle2faSecret($google2faSecret)
    {
        $this->google2faSecret = $google2faSecret;

        return $this;
    }

    /**
     * @return string
     */
    public function getGoogle2faRecoveryCodes()
    {
        return $this->google2faRecoveryCodes;
    }

    /**
     * @param string $google2faRecoveryCodes
     *
     * @return self
     */
    public function setGoogle2faRecoveryCodes($google2faRecoveryCodes)
    {
        $this->google2faRecoveryCodes = $google2faRecoveryCodes;

        return $this;
    }

    /**
     * Route notifications for the Nexmo channel.
     *
     * @param \Illuminate\Notifications\Notification $notification
     *
     * @return string
     */
    public function routeNotificationForNexmo($notification)
    {
        return $this->profile->getContactNumber();
    }
}
