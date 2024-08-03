<?php

namespace HMS\Prometheus\Collectors\Instrumentation;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use HMS\Facades\Meta;
use HMS\Repositories\Instrumentation\SensorBatteryRepository;
use Spatie\Prometheus\Collectors\Collector;
use Spatie\Prometheus\Facades\Prometheus;

class SensorBatteryCollector implements Collector
{
    public function register(): void
    {
        Prometheus::addGauge('Sensor Battery Status')
            ->name('instrumentation_sensor_battery')
            ->helpText('Battery voltage (V)')
            ->label('sensor')
            ->value(fn () => app()->call([$this, 'getValue']));
    }

    public function getValue(SensorBatteryRepository $sensorBatteryRepository)
    {
        $values = [];

        foreach ($sensorBatteryRepository->findAll() as $sensor) {
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
