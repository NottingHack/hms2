<?php

namespace HMS\Repositories;

use Doctrine\ORM\EntityRepository;
use LaravelDoctrine\ORM\Pagination\Paginatable;

class LinkRepository extends EntityRepository
{
    use Paginatable;

    public function save(Link $link)
    {
        $this->_em->persist($link);
        $this->_em->flush();
    }
}
