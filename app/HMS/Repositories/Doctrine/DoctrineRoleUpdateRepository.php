<?php

namespace HMS\Repositories\Doctrine;

use HMS\Entities\Role;
use HMS\Entities\User;
use HMS\Entities\RoleUpdate;
use Doctrine\ORM\EntityRepository;
use HMS\Repositories\RoleUpdateRepository;

class DoctrineRoleUpdateRepository extends EntityRepository implements RoleUpdateRepository
{
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
