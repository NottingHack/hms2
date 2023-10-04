<?php

namespace HMS\Entities;

use Carbon\Carbon;

class RoleUpdate
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var null|Role
     */
    protected $roleAdded;

    /**
     * @var null|Role
     */
    protected $roleRemoved;

    /**
     * @var Carbon
     */
    protected $createdAt;

    /**
     * @var User|null
     */
    protected $updateBy;

    /**
     * @var string|null
     */
    protected $reason;

    /**
     * Construct a new Role Update.
     *
     * @param User $user
     * @param Role|null $roleAdded
     * @param Role|null $roleRemoved
     * @param User|null $updateBy
     */
    public function __construct(
        User $user,
        ?Role $roleAdded = null,
        ?Role $roleRemoved = null,
        ?User $updateBy = null,
        ?string $reason = null
    ) {
        $this->user = $user;
        $this->roleAdded = $roleAdded;
        $this->roleRemoved = $roleRemoved;
        $this->createdAt = Carbon::now();
        $this->updateBy = $updateBy;
        $this->reason = $reason;
    }

    /**
     * Gets the value of id.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Gets the value of user.
     *
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Gets the value of roleAdded.
     *
     * @return null|Role
     */
    public function getRoleAdded(): ?Role
    {
        return $this->roleAdded;
    }

    /**
     * Gets the value of roleRemoved.
     *
     * @return null|Role
     */
    public function getRoleRemoved(): ?Role
    {
        return $this->roleRemoved;
    }

    /**
     * Gets the value of createdAt.
     *
     * @return Carbon
     */
    public function getCreatedAt(): Carbon
    {
        return $this->createdAt;
    }

    /**
     * @return User|null
     */
    public function getUpdateBy()
    {
        return $this->updateBy;
    }

    /**
     * Gets the reason for the role update.
     *
     * @return string|null
     */
    public function getReason()
    {
        return $this->reason;
    }
}
