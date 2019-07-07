<?php

namespace HMS\Repositories\Instrumentation\Doctrine;

use Doctrine\ORM\EntityRepository;
use HMS\Entities\Instrumentation\Event;
use HMS\Entities\Instrumentation\Service;
use HMS\Repositories\Instrumentation\EventRepository;
use LaravelDoctrine\ORM\Pagination\PaginatesFromRequest;

class DoctrineEventRepository extends EntityRepository implements EventRepository
{
    use PaginatesFromRequest;

    /**
     * @param Service $service
     * @param int    $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateByService(Service $service, $perPage = 50, $pageName = 'page')
    {
        $q = parent::createQueryBuilder('event')
            ->where('event.value = :service_name')
            ->orderBy('event.time', 'DESC');

        $q = $q->setParameter('service_name', $service->getName())->getQuery();

        return $this->paginate($q, $perPage, $pageName);
    }

    /**
     * save Event to the DB.
     * @param  Event $event
     */
    public function save(Event $event)
    {
        $this->_em->persist($event);
        $this->_em->flush();
    }
}
