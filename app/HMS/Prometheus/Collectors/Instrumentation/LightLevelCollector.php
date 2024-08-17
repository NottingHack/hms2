<?php

namespace HMS\Prometheus\Collectors\Instrumentation;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use HMS\Facades\Meta;
use HMS\Repositories\Instrumentation\LightLevelRepository;
use Spatie\Prometheus\Collectors\Collector;
use Spatie\Prometheus\Facades\Prometheus;

class LightLevelCollector implements Collector
{
    public function register(): void
    {
        Prometheus::addGauge('Light Sensors')
            ->name('instrumentation_light_level')
            ->helpText('Light Level in Lux')
            ->label('sensor')
            ->value(fn () => app()->call([$this, 'getValue']));
    }

    public function getValue(LightLevelRepository $lightLevelRepository)
    {
        $values = [];

        foreach ($lightLevelRepository->findAll() as $sensor) {
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
    }
}
