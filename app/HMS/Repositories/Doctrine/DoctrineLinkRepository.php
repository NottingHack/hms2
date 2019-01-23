<?php

namespace HMS\Repositories\Doctrine;

use HMS\Entities\Link;
use Doctrine\ORM\EntityRepository;
use HMS\Repositories\LinkRepository;
use LaravelDoctrine\ORM\Pagination\PaginatesFromRequest;

class DoctrineLinkRepository extends EntityRepository implements LinkRepository
{
    use PaginatesFromRequest;

    /**
     * Save Link to the DB.
     *
     * @param Link $link
     */
    public function save(Link $link)
    {
        $this->_em->persist($link);
        $this->_em->flush();
    }

    /**
     * Remove a Link from the DB.
     *
     * @param Link $link
     */
    public function remove(Link $link)
    {
        $this->_em->remove($link);
        $this->_em->flush();
    }
}
