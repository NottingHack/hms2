<?php

namespace HMS\Repositories\Doctrine;

use HMS\Entities\Profile;
use Doctrine\ORM\EntityRepository;
use HMS\Repositories\ProfileRepository;

class DoctrineProfileRepository extends EntityRepository implements ProfileRepository
{
    /**
     * save Profile to the DB.
     * @param  Profile $profile
     */
    public function save(Profile $profile)
    {
        $this->_em->persist($profile);
        $this->_em->flush();
    }
}
