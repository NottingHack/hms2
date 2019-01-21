<?php

namespace HMS\Entities;

use Carbon\Carbon;
use HMS\Traits\Entities\SoftDeletable;
use LaravelDoctrine\Extensions\Timestamps\Timestamps;

class Meta
{
    use SoftDeletable, Timestamps;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $value;

    /**
     * @param string $key
     */
    public function create($key)
    {
        $this->key = $key;
        $now = Carbon::now();
        $this->setCreatedAt($now);
        $this->setUpdatedAt($now);

        return $this;
    }

    /**
     * Gets the value of key.
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Gets the value of value.
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Sets the value of value.
     *
     * @param string $value the value
     *
     * @return self
     */
    public function setValue($value): self
    {
        $this->value = $value;

        return $this;
    }
}
