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
     * @param  $id
     * @return array
     */
    public function find($id)
    {
        return parent::find($id);
    }

    /**
     * @param  User   $user
     * @return array
     */
    public function findByUser(User $user)
    {
        return parent::findByUser($user);
    }

    /**
     * find the lastest roleUpdate when this User was give the Role.
     * @param  Role  $role
     * @param  User  $user
     * @return null|RoleUpdate
     */
    public function findLatestRoleAddedByUser(Role $role, User $user)
    {
        return parent::findOneBy(['user' => $user, 'roleAdded' => $role], ['createdAt' => 'DESC']);
    }

    /**
     * save RoleUpdate to the DB.
     * @param  RoleUpdate $roleUpdate
     */
    public function save(RoleUpdate $roleUpdate)
    {
        $this->_em->persist($roleUpdate);
        $this->_em->flush();
    }
}
