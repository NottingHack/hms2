<?php

namespace HMS\Entities\Banking\Stripe;

use Carbon\Carbon;
use HMS\Traits\Entities\Timestampable;

class Event
{
    use Timestampable;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var null|Carbon
     */
    protected $handledAt;

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
     * @param int $id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return null|Carbon
     */
    public function getHandledAt()
    {
        return $this->handledAt;
    }

    /**
     * @param null|Carbon $handledAt
     *
     * @return self
     */
    public function setHandledAt(?Carbon $handledAt)
    {
        $this->handledAt = $handledAt;

        return $this;
    }
}
