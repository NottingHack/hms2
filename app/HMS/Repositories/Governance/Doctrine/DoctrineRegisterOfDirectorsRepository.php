<?php

namespace HMS\Repositories\Governance\Doctrine;

use Doctrine\ORM\EntityRepository;
use HMS\Entities\Governance\RegisterOfDirectors;
use HMS\Entities\User;
use HMS\Repositories\Governance\RegisterOfDirectorsRepository;
use LaravelDoctrine\ORM\Pagination\PaginatesFromRequest;

class DoctrineRegisterOfDirectorsRepository extends EntityRepository implements RegisterOfDirectorsRepository
{
    use PaginatesFromRequest;

    /**
     * @return RegisterOfDirectors[]
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
     * @return null|RegisterOfDirectors
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
        $query = $this->createQueryBuilder('rod')
            ->orderBy('rod.startedAt', 'ASC')
            ->addOrderBy('rod.lastname', 'ASC')
            ->addOrderBy('rod.firstname', 'ASC')
            ->getQuery();

        return $this->paginate($query, $perPage, $pageName, false);
    }

    /**
     * Save RegisterOfDirectors to the DB.
     *
     * @param RegisterOfDirectors $registerOfDirectors
     */
    public function save(RegisterOfDirectors $registerOfDirectors)
    {
        $this->_em->persist($registerOfDirectors);
        $this->_em->flush();
    }
}
