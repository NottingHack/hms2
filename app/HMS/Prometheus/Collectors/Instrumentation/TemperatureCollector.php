<?php

namespace HMS\Prometheus\Collectors\Instrumentation;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use HMS\Facades\Meta;
use HMS\Repositories\Instrumentation\TemperatureRepository;
use Spatie\Prometheus\Collectors\Collector;
use Spatie\Prometheus\Facades\Prometheus;

class TemperatureCollector implements Collector
{
    public function register(): void
    {
        Prometheus::addGauge('Temperature Sensors')
            ->name('instrumentation_temperature')
            ->helpText('Temperature in degree celsius (°C)')
            ->label('sensor')
            ->value(fn () => app()->call([$this, 'getValue']));
    }

    public function getValue(TemperatureRepository $temperatureRepository)
    {
        $values = [];

        foreach ($temperatureRepository->findAll() as $sensor) {
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
                [str($sensor->getName())->replace('-LLAP', '')],
            ];
        }

        return $values;
    }
}
