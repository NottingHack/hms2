<?php

namespace HMS\Repositories\Banking\Stripe;

use HMS\Entities\Banking\Stripe\Event;

interface EventRepository
{
    /**
     * Find a Event by id.
     *
     * @param string $id
     *
     * @return null|Event
     */
    public function findOneById(string $id);

    /**
     * Save Event to the DB.
     *
     * @param Event $event
     *
     * @return Event
     */
    public function save(Event $event);
}
