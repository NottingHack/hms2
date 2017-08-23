<?php

namespace HMS\Repositories\GateKeeper\Doctrine;

use HMS\Entities\User;
use Doctrine\ORM\EntityRepository;
use HMS\Entities\GateKeeper\RfidTag;
use HMS\Repositories\GateKeeper\RfidTagRepository;
use LaravelDoctrine\ORM\Pagination\PaginatesFromRequest;

class DoctrineRfidTagRepository extends EntityRepository implements RfidTagRepository
{
    use PaginatesFromRequest;

    /**
     * @return RfidTag[]
     */
    public function findAll()
    {
        return parent::findAll();
    }

    /**
     * @param  User $user
     * @return RfidTag[]
     */
    public function findByUser(User $user)
    {
        return parent::findByUser($user);
    }

    /**
     * @param User   $user
     * @param int    $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateByUser(User $user, $perPage = 15, $pageName = 'page')
    {
        $q = parent::createQueryBuilder('pin')
            ->where('user_id = :user_id');

        $q = $q->setParameter('user_id', $user->getId())->getQuery();

        return $this->paginate($q, $perPage, $pageName);
    }

    /**
     * save RfidTag to the DB.
     * @param  RfidTag $rfidTag
     */
    public function save(RfidTag $rfidTag)
    {
        $this->_em->persist($rfidTag);
        $this->_em->flush();
    }

    /**
     * remove a RfidTag from the DB.
     * @param RfidTag $rfidTag
     */
    public function remove(RfidTag $rfidTag){
        $this->_em->remove($rfidTag);
        $this->_em->flush();
    }
}
