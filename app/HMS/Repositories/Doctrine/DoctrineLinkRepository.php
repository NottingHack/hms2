<?php

namespace HMS\Repositories\Doctrine;

use HMS\Entities\Link;
use Doctrine\ORM\EntityRepository;
use LaravelDoctrine\ORM\Pagination\Paginatable;

class DoctrineLinkRepository extends EntityRepository implements LinkRepository
{
    use Paginatable;

    /**
     * save Link to the DB.
     * @param  User $user
     */
    public function save(Link $link)
    {
        $this->_em->persist($link);
        $this->_em->flush();
    }
}
