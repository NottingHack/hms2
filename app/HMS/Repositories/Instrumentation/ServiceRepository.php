<?php

namespace HMS\Repositories\Instrumentation;

use HMS\Entities\Instrumentation\Service;

interface ServiceRepository
{
    /**
     * Finds all entities in the repository.
     *
     * @return array The entities.
     */
    public function findAll();

    /**
     * save Service to the DB.
     *
     * @param  Service $service
     */
    public function save(Service $service);
}
