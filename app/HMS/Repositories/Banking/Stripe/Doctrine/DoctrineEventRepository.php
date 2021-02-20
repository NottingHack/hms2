<?php

namespace HMS\Repositories\Banking\Stripe\Doctrine;

use Doctrine\ORM\EntityRepository;
use HMS\Entities\Banking\Stripe\Event;
use HMS\Repositories\Banking\Stripe\EventRepository;

class DoctrineEventRepository extends EntityRepository implements EventRepository
{
    /**
     * Find a Event by id.
     *
     * @param string $id
     *
     * @return null|Event
     */
    public function findOneById(string $id)
    {
        return parent::findOneById($id);
    }

    /**
     * Save Event to the DB.
     *
     * @param Event $event
     *
     * @return Event
     */
    public function save(Event $event)
    {
        $this->_em->persist($event);
        $this->_em->flush();

        return $event;
    }
}
