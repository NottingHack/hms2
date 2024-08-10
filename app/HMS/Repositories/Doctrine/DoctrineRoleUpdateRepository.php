<?php

namespace HMS\Repositories\Doctrine;

use Doctrine\ORM\EntityRepository;
use HMS\Entities\Role;
use HMS\Entities\RoleUpdate;
use HMS\Entities\User;
use HMS\Repositories\RoleUpdateRepository;
use LaravelDoctrine\ORM\Pagination\PaginatesFromRequest;

class DoctrineRoleUpdateRepository extends EntityRepository implements RoleUpdateRepository
{
    use PaginatesFromRequest;

    /**
     * @param int $id
     *
     * @return null|RoleUpdate
     */
    public function findOneById(int $id)
    {
        return parent::findOneById($id);
    }

    /**
     * @param User $user
     *
     * @return array
     */
    public function findByUser(User $user)
    {
        return parent::findByUser($user);
    }

    /**
     * Find the lastest roleUpdate when this User was give the Role.
     *
     * @param Role $role
     * @param User $user
     *
     * @return null|RoleUpdate
     */
    public function findLatestRoleAddedByUser(Role $role, User $user)
    {
        return parent::findOneBy(['user' => $user, 'roleAdded' => $role], ['createdAt' => 'DESC']);
    }

    /**
     * @param User $user
     * @param int $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateByUser(User $user, $perPage = 15, $pageName = 'page')
    {
        $q = parent::createQueryBuilder('roleUpdate')
            ->where('roleUpdate.user = :user_id')
            ->orderBy('roleUpdate.createdAt', 'ASC');

        $q = $q->setParameter('user_id', $user->getId())->getQuery();

        return $this->paginate($q, $perPage, $pageName);
    }

    /**
     * Save RoleUpdate to the DB.
     *
     * @param RoleUpdate $roleUpdate
     */
    public function save(RoleUpdate $roleUpdate)
    {
        $this->_em->persist($roleUpdate);
        $this->_em->flush();
    }
}
