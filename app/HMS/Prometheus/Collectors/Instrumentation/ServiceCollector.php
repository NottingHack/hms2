<?php

namespace HMS\Prometheus\Collectors\Instrumentation;

use HMS\Repositories\Instrumentation\ServiceRepository;
use Spatie\Prometheus\Collectors\Collector;
use Spatie\Prometheus\Facades\Prometheus;

class ServiceCollector implements Collector
{
    public function register(): void
    {
        Prometheus::addGauge('Instrumentation Service Status')
            ->name('instrumentation_service_status')
            ->helpText('Service Status')
            ->label('service')
            ->value(fn () => app()->call([$this, 'getValue']));
    }

    public function getValue(ServiceRepository $serviceRepository)
    {
        $values = [];

        foreach ($serviceRepository->findAll() as $service) {
            $values[] = [
                $service->getStatus(),
                [$service->getName()],
            ];
        }

        return $values;
    }
}
