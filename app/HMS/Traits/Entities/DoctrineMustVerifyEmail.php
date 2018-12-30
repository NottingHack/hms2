<?php

namespace HMS\Traits\Entities;

use Carbon\Carbon;
use Illuminate\Auth\Notifications\VerifyEmail;
use LaravelDoctrine\ORM\Facades\EntityManager;

trait DoctrineMustVerifyEmail
{
    /**
     * @var null|Carbon Has the email address be verified
     */
    protected $emailVerifiedAt;

    /**
     * Determine if the user has verified their email address.
     *
     * @return bool
     */
    public function hasVerifiedEmail()
    {
        return ! is_null($this->emailVerifiedAt);
    }

    /**
     * Mark the given user's email as verified.
     *
     * @return bool
     */
    public function markEmailAsVerified()
    {
        $repository = EntityManager::getRepository(get_class($this));
        $this->emailVerifiedAt = new Carbon;
        $repository->save($this);

        return true;
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail);
    }

    /**
     * @return null|Carbon Has the email address be verified
     */
    public function getEmailVerifiedAt()
    {
        return $this->emailVerifiedAt;
    }

    /**
     * @param null|Carbon Has the email address be verified $emailVerifiedAt
     *
     * @return self
     */
    public function setEmailVerifiedAt($emailVerifiedAt)
    {
        $this->emailVerifiedAt = $emailVerifiedAt;

        return $this;
    }
}
