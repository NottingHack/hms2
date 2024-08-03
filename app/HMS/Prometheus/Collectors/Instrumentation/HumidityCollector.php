<?php

namespace HMS\Prometheus\Collectors\Instrumentation;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use HMS\Facades\Meta;
use HMS\Repositories\Instrumentation\HumidityRepository;
use Spatie\Prometheus\Collectors\Collector;
use Spatie\Prometheus\Facades\Prometheus;

class HumidityCollector implements Collector
{
    public function register(): void
    {
        Prometheus::addGauge('Humidity Sensors')
            ->name('instrumentation_humidity')
            ->helpText('Relative Humidity (%)')
            ->label('sensor')
            ->value(function () {
                $humidityRepository = app(HumidityRepository::class);
                $values = [];

                foreach ($humidityRepository->findAll() as $sensor) {
                    if (is_null($sensor->getName())) {
                        continue;
                    }

                    if ($sensor->getTime()->isBefore(
                        Carbon::now()->sub(
                            CarbonInterval::create(
                                Meta::get('prometheus_instrumentation_sensors_timeout', 'P30M')
                            )
                        )
                    )) {
                        continue;
                    }

                    $values[] = [
                        $sensor->getReading(),
                        [$sensor->getName()],
                    ];
                }

                return $values;
            });
    }
}
