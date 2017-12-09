<?php

namespace HMS\Repositories\Members\Doctrine;

use HMS\Entities\User;
use HMS\Entities\Members\Box;
use Doctrine\ORM\EntityRepository;
use HMS\Repositories\Members\BoxRepository;
use LaravelDoctrine\ORM\Pagination\PaginatesFromRequest;

class DoctrineBoxRepository extends EntityRepository implements BoxRepository
{
    use PaginatesFromRequest;

    /**
     * @param User   $user
     * @param int    $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateByUser(User $user, $perPage = 15, $pageName = 'page')
    {
        $q = parent::createQueryBuilder('box')
            ->where('box.user = :user_id');

        $q = $q->setParameter('user_id', $user->getId())->getQuery();

        return $this->paginate($q, $perPage, $pageName);
    }
    /**
     * save Box to the DB. 
     * @param  Box $box
     */
    public function save(Box $box)
    {
        $this->_em->persist($box);
        $this->_em->flush();
    }
}
