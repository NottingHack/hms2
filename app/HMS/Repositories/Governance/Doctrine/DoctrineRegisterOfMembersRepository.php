<?php

namespace HMS\Repositories\Governance\Doctrine;

use Doctrine\ORM\EntityRepository;
use HMS\Entities\Governance\RegisterOfMembers;
use HMS\Entities\User;
use HMS\Repositories\Governance\RegisterOfMembersRepository;
use LaravelDoctrine\ORM\Pagination\PaginatesFromRequest;

class DoctrineRegisterOfMembersRepository extends EntityRepository implements RegisterOfMembersRepository
{
    use PaginatesFromRequest;

    /**
     * @return RegisterOfMembers[]
     */
    public function findAll()
    {
        return parent::findBy(
            [],
            [
                'startedAt' => 'ASC',
                'lastname' => 'ASC',
                'firstname' => 'ASC',
            ]
        );
    }

    /**
     * Find the Current register entry for the given User
     *
     * @param User $user
     *
     * @return null|RegisterOfMembers
     */
    public function findCurrentByUser(User $user)
    {
        return parent::findOneBy(
            [
                'user' => $user,
                'endedAt' => null,
            ],
            [
                'startedAt' => 'DESC',
            ]
        );
    }

    /**
     * @param int $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateAll($perPage = 15, $pageName = 'page')
    {
        $query = $this->createQueryBuilder('rom')
            ->orderBy('rom.startedAt', 'ASC')
            ->addOrderBy('rom.lastname', 'ASC')
            ->addOrderBy('rom.firstname', 'ASC')
            ->getQuery();

        return $this->paginate($query, $perPage, $pageName, false);
    }

    /**
     * Save RegisterOfMembers to the DB.
     *
     * @param RegisterOfMembers $registerOfMembers
     */
    public function save(RegisterOfMembers $registerOfMembers)
    {
        $this->_em->persist($registerOfMembers);
        $this->_em->flush();
    }
}
