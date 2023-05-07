<?php

namespace HMS\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use HMS\Entities\Banking\Account;
use HMS\Entities\Gatekeeper\Pin;
use HMS\Entities\Gatekeeper\RfidTag;
use HMS\Helpers\Discord;
use HMS\Traits\Entities\DoctrineMustVerifyEmail;
use HMS\Traits\Entities\SoftDeletable;
use HMS\Traits\Entities\Timestampable;
use HMS\Traits\HasApiTokens;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Support\Str;
use LaravelDoctrine\ACL\Contracts\HasPermissions as HasPermissionsContract;
use LaravelDoctrine\ACL\Contracts\HasRoles as HasRoleContract;
use LaravelDoctrine\ACL\Permissions\HasPermissions;
use LaravelDoctrine\ACL\Roles\HasRoles;
use LaravelDoctrine\ORM\Notifications\Notifiable;

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

    /**
     * Min Password length.
     */
    public const MIN_PASSWORD_LENGTH = 8;

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
     * @var string Users password ONLY when using the Doctrine PasswordStore
     */
    protected $password;

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
     * @var \Doctrine\Common\Collections\Collection|RfidTag[]
     */
    protected $rfidTags;

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
        $this->password = '';
        $this->roles = new ArrayCollection();
        $this->emails = new ArrayCollection();
        $this->rfidTags = new ArrayCollection();
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
        return 'id';
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
     * Get the password for the User.
     * ONLY relevant when using the Doctrine PasswordStore.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the password for the User.
     * ONLY relevant when using the Doctrine PasswordStore.
     *
     * @param string $password
     *
     * @return self
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        // TODO: when using Doctrine PasswordStore
        // return $this->getPassword();
        // else
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
     * @return \Doctrine\Common\Collections\Collection|Role[]
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection|\LaravelDoctrine\ACL\Permissions\Permission[]
     */
    public function getPermissions()
    {
        // user's don't directly have permissions, only via their roles
        return [];
    }

    /**
     * @return null|Profile The users profile
     */
    public function getProfile(): ?Profile
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
    public function getAccount(): ?Account
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
     * @return \Doctrine\Common\Collections\Collection|Email[]
     */
    public function getEmails()
    {
        return $this->emails;
    }

    /**
     * @return null|HMS\Entities\Gatekeeper\Pin
     */
    public function getPin()
    {
        return $this->pin;
    }

    /**
     * @param null|HMS\Entities\Gatekeeper\Pin $pin
     *
     * @return self
     */
    public function setPin(Pin $pin)
    {
        $this->pin = $pin;

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection|RfidTag[]
     */
    public function getRfidTags()
    {
        return $this->rfidTags;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection|RfidTag[] $rfidTags
     *
     * @return self
     */
    public function setRfidTags($rfidTags)
    {
        $this->rfidTags = $rfidTags;

        return $this;
    }

    /**
     * Get the member role if this user has one.
     *
     * @return null|Role
     */
    public function getMemberRole(): ?Role
    {
        return $this->getRoles()->filter(function (Role $role) {
            return Str::startsWith($role->getName(), 'member.');
        })->first() ?: null;
    }

    /**
     * Get the member status as a string.
     *
     * @return string
     */
    public function getMemberStatusString(): string
    {
        return $this->getMemberRole() ? $this->getMemberRole()->getDisplayName() : 'Not a Member';
    }

    /**
     * Route notifications to the Discord DM channel.
     *
     * @return null|string
     */
    public function routeNotificationForDiscord()
    {
        if (! config('services.discord.token') ||
            ! $this->getProfile()->getDiscordUserId()) {
            return null;
        }

        $discord = new Discord(
            config('services.discord.token'),
            config('services.discord.guild_id')
        );

        $discordUserId = $this->getProfile()->getDiscordUserId();
        $discordMember = $discord->findMemberByUsername($discordUserId);

        if (! $discordMember) {
            return null;
        }

        return $discord->getDiscordClient()->user->createDm([
            'recipient_id' => $discordMember->user->id,
        ])->id;
    }
}
