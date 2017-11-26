<?php

namespace HMS\Entities;

use Carbon\Carbon;

class PasswordReset
{
    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var Carbon
     */
    protected $created;

    /**
     * Gets the value of email.
     *
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Sets the value of email.
     *
     * @param string $email the email
     *
     * @return self
     */
    public function setEmail($email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Gets the value of token.
     *
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * Sets the value of token.
     *
     * @param string $token the token
     *
     * @return self
     */
    public function setToken($token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Gets the value of created.
     *
     * @return Carbon
     */
    public function getCreated(): Carbon
    {
        return $this->created;
    }

    /**
     * Sets the value of created.
     *
     * @param Carbon $created the created
     *
     * @return self
     */
    public function setCreated(Carbon $created): self
    {
        $this->created = $created;

        return $this;
    }
}
