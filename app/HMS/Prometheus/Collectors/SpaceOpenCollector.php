<?php

namespace HMS\Prometheus\Collectors;

use Illuminate\Support\Facades\DB;
use Spatie\Prometheus\Collectors\Collector;
use Spatie\Prometheus\Facades\Prometheus;

class SpaceOpenCollector implements Collector
{
    public function register(): void
    {
        $temps = DB::select('CALL sp_get_space_status(@space_open, @last_change)');
        $results = DB::select('SELECT @space_open AS space_open, @last_change AS last_change');
        $spaceOpen = $results[0]->space_open;
        $lastChange = $results[0]->last_change;

        Prometheus::addGauge('Space Open Status')
            ->name('space_open')
            ->helpText('Is the space currently open or closed')
            ->value(fn () => $spaceOpen == 'Yes');
    }
}
