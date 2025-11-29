<?php

namespace HMS\Traits\Entities;

use Carbon\Carbon;

trait SoftDeletable
{
    /**
     * @var Carbon
     */
    protected $deletedAt;

    /**
     * Sets deletedAt.
     *
     * @param Carbon|null $deletedAt
     *
     * @return $this
     */
    public function setDeletedAt(?Carbon $deletedAt = null)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * Returns deletedAt.
     *
     * @return Carbon
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * Is deleted?
     *
     * @return bool
     */
    public function isDeleted()
    {
        return null !== $this->deletedAt;
    }
}
