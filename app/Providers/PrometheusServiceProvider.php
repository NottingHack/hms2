<?php

namespace App\Providers;

use HMS\Prometheus\Collectors\Instrumentation\BarometricPressureCollector;
use HMS\Prometheus\Collectors\Instrumentation\HumidityCollector;
use HMS\Prometheus\Collectors\Instrumentation\LightLevelCollector;
use HMS\Prometheus\Collectors\Instrumentation\MacAddressCountCollector;
use HMS\Prometheus\Collectors\Instrumentation\SensorBatteryCollector;
use HMS\Prometheus\Collectors\Instrumentation\TemperatureCollector;
use HMS\Prometheus\Collectors\SpaceOpenCollector;
use HMS\Prometheus\Collectors\Statistics\BoxUsageCollector;
use HMS\Prometheus\Collectors\Statistics\MembersshipStatisticsCollector;
use HMS\Prometheus\Collectors\Statistics\ToolUsageCollector;
use Illuminate\Support\ServiceProvider;
use Spatie\Prometheus\Collectors\Horizon\CurrentMasterSupervisorCollector;
use Spatie\Prometheus\Collectors\Horizon\CurrentProcessesPerQueueCollector;
use Spatie\Prometheus\Collectors\Horizon\CurrentWorkloadCollector;
use Spatie\Prometheus\Collectors\Horizon\FailedJobsPerHourCollector;
use Spatie\Prometheus\Collectors\Horizon\HorizonStatusCollector;
use Spatie\Prometheus\Collectors\Horizon\JobsPerMinuteCollector;
use Spatie\Prometheus\Collectors\Horizon\RecentJobsCollector;
use Spatie\Prometheus\Facades\Prometheus;

class PrometheusServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerHorizonCollectors();
    }

    public function boot(): void
    {
        /*
         * Add HMS collectors here so that the RepositoryServiceProvider has done its thing
         */
        Prometheus::registerCollectorClasses([
            SpaceOpenCollector::class,
        ]);

        $this->registerInstrumentaionCollectors();
        $this->registerStatisticsCollectors();
    }

    public function registerHorizonCollectors(): self
    {
        Prometheus::registerCollectorClasses([
            CurrentMasterSupervisorCollector::class,
            CurrentProcessesPerQueueCollector::class,
            CurrentWorkloadCollector::class,
            FailedJobsPerHourCollector::class,
            HorizonStatusCollector::class,
            JobsPerMinuteCollector::class,
            RecentJobsCollector::class,
        ]);

        return $this;
    }

    public function registerInstrumentaionCollectors(): self
    {
        Prometheus::registerCollectorClasses([
            BarometricPressureCollector::class,
            HumidityCollector::class,
            LightLevelCollector::class,
            MacAddressCountCollector::class,
            SensorBatteryCollector::class,
            TemperatureCollector::class,
        ]);

        return $this;
    }

    public function registerStatisticsCollectors(): self
    {
        Prometheus::registerCollectorClasses([
            BoxUsageCollector::class,
            MembersshipStatisticsCollector::class,
            ToolUsageCollector::class,
        ]);

        return $this;
    }
}
