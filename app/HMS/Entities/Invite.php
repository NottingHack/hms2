<?php

namespace HMS\Entities;

use Carbon\Carbon;
use HMS\Traits\Entities\SoftDeletable;
use HMS\Traits\Entities\Timestampable;
use LaravelDoctrine\ORM\Notifications\Notifiable;

class Invite
{
    use Notifiable, SoftDeletable, Timestampable;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string email
     */
    protected $email;

    /**
     * @var null|string invite token
     */
    protected $inviteToken;

    /**
     * Invite constructor.
     * Invite Token is auto generated.
     *
     * @param $email
     * @return Invite
     */
    public function create($email)
    {
        $this->email = $email;
        $now = Carbon::now();
        $this->inviteToken = hash_hmac('sha256', str_random(40) . (string) $now, config('app.key'));
        $this->setCreatedAt($now);
        $this->setUpdatedAt($now);

        return $this;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Get the token value.
     * @retrun null|string
     */
    public function getInviteToken(): ?string
    {
        return $this->inviteToken;
    }
}
