<?php

namespace HMS\Prometheus\Collectors\Instrumentation;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use HMS\Facades\Meta;
use HMS\Repositories\Instrumentation\BarometricPressureRepository;
use Spatie\Prometheus\Collectors\Collector;
use Spatie\Prometheus\Facades\Prometheus;

class BarometricPressureCollector implements Collector
{
    public function register(): void
    {
        Prometheus::addGauge('Barometric Pressure Sensors')
            ->name('instrumentation_barometric_pressure')
            ->helpText('Barometric Pressure in millibars (hPa)')
            ->label('sensor')
            ->value(function () {
                $barometricPressureRepository = app(BarometricPressureRepository::class);
                $values = [];

                foreach ($barometricPressureRepository->findAll() as $sensor) {
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
