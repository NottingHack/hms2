<?php

namespace HMS\Repositories\Doctrine;

use HMS\Entities\Role;
use HMS\Entities\Email;
use Doctrine\ORM\EntityRepository;
use HMS\Repositories\EmailRepository;

// TODO: findByUserPaginate(????);
class DoctrineEmailRepository extends EntityRepository implements EmailRepository
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
     * @param  Role $role
     * @return array
     */
    public function findByRole(Role $role)
    {
        return parent::findByRole($role);
    }

    /**
     * save Email to the DB.
     * @param  Email $email
     */
    public function save(Email $email)
    {
        $this->_em->persist($email);
        $this->_em->flush();
    }
}
