<?php

namespace HMS\Repositories\Instrumentation\Doctrine;

use Doctrine\ORM\EntityRepository;
use HMS\Entities\Instrumentation\Service;
use HMS\Repositories\Instrumentation\ServiceRepository;

class DoctrineServiceRepository extends EntityRepository implements ServiceRepository
{
    /**
     * Finds all entities in the repository.
     *
     * @return array The entities.
     */
    public function findAll()
    {
        return parent::findAll();
    }

    /**
     * save Service to the DB.
     * @param  Service $service
     */
    public function save(Service $service)
    {
        $this->_em->persist($service);
        $this->_em->flush();
    }
}
