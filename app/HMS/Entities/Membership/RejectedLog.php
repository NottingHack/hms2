<?php

namespace HMS\Entities\Membership;

use Carbon\Carbon;
use HMS\Entities\User;
use HMS\Traits\Entities\Timestampable;

class RejectedLog
{
    use Timestampable;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var User
     */
    protected $user;
    /**
     * @var string
     */
    protected $reason;
    /**
     * @var User
     */
    protected $rejectedBy;
    /**
     * @var Carbon|null
     */
    protected $userUpdatedAt;

    /**
     * Gets the value of id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return self
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * @param string $reason
     *
     * @return self
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * @return User
     */
    public function getRejectedBy()
    {
        return $this->rejectedBy;
    }

    /**
     * @param User $rejectedBy
     *
     * @return self
     */
    public function setRejectedBy(User $rejectedBy)
    {
        $this->rejectedBy = $rejectedBy;

        return $this;
    }

    /**
     * @return Carbon|null
     */
    public function getUserUpdatedAt()
    {
        return $this->userUpdatedAt;
    }

    /**
     * @param Carbon|null $userUpdatedAt
     *
     * @return self
     */
    public function setUserUpdatedAt(?Carbon $userUpdatedAt)
    {
        $this->userUpdatedAt = $userUpdatedAt;

        return $this;
    }
}
