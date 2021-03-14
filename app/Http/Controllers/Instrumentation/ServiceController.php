<?php

namespace App\Http\Controllers\Instrumentation;

use App\Http\Controllers\Controller;
use HMS\Entities\Instrumentation\Service;
use HMS\Repositories\Instrumentation\EventRepository;
use HMS\Repositories\Instrumentation\ServiceRepository;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * @var ServiceRepository
     */
    protected $serviceRepository;

    /**
     * @var EventRepository
     */
    protected $eventRepository;

    /**
     * Construct a new Controller.
     *
     * @param ServiceRepository $serviceRepository
     */
    public function __construct(
        ServiceRepository $serviceRepository,
        EventRepository $eventRepository
    ) {
        $this->serviceRepository = $serviceRepository;
        $this->eventRepository = $eventRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function status()
    {
        $services = $this->serviceRepository->findAll();

        return view('instrumentation.status')
            ->with('services', $services);
    }

    public function eventsForService(Service $service, Request $request)
    {
        $events = $this->eventRepository->paginateByService($service);

        return view('instrumentation.eventsForService')
            ->with('service', $service)
            ->with('events', $events);
    }
}
