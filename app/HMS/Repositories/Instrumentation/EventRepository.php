<?php

namespace HMS\Repositories\Instrumentation;

use HMS\Entities\Instrumentation\Event;
use HMS\Entities\Instrumentation\Service;

interface EventRepository
{
    /**
     * @param Service $service
     * @param int    $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateByService(Service $service, $perPage = 15, $pageName = 'page');

    /**
     * save Event to the DB.
     * @param  Event $event
     */
    public function save(Event $event);
}
