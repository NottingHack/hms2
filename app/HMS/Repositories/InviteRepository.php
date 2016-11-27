<?php

namespace HMS\Repositories;

use Hms\Entities\Invite;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;

class InviteRepository extends EntityRepository
{
    /**
     * Create a new invite if not found
     *
     * @param  string $email
     * @return HMS\Entities\Invite
     */
    public function findOrCreateByEmail($email)
    {
        $invite = $this->findOneByEmail($email);
        if (!$invite) {
            // dont have a previous invite so create one
            $invite = new Invite();
            $invite->create($email);
            $this->_em->persist($invite);
            $this->_em->flush();
        }
        return $invite;
    }

}
