<?php

namespace HMS\Traits\Entities;

use Carbon\Carbon;

trait Timestampable
{
    /**
     * @var Carbon
     */
    protected $createdAt;

    /**
     * @var Carbon
     */
    protected $updatedAt;

    /**
     * Sets createdAt.
     *
     * @param  Carbon $createdAt
     * @return $this
     */
    public function setCreatedAt(Carbon $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Returns createdAt.
     *
     * @return Carbon
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Sets updatedAt.
     *
     * @param  Carbon $updatedAt
     * @return $this
     */
    public function setUpdatedAt(Carbon $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Returns updatedAt.
     *
     * @return Carbon
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
