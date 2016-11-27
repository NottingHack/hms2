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
     * @var string invite token
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
        $this->inviteToken = hash_hmac('sha256', str_random(40), config('app.key'));
        $now = Carbon::now();
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
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Get the token value.
     * @return string
     */
    public function getInviteToken()
    {
        return $this->inviteToken;
    }
}
