<?php

namespace HMS\Prometheus\Collectors\Statistics;

use HMS\Facades\Meta;
use HMS\Repositories\Members\BoxRepository;
use Spatie\Prometheus\Collectors\Collector;
use Spatie\Prometheus\Facades\Prometheus;

class BoxUsageCollector implements Collector
{
    public function register(): void
    {
        Prometheus::addGauge('Member Boxes count')
            ->label('count_of')
            ->helpText('Members Box usages')
            ->value(fn () => app()->call([$this, 'getValue']));
    }

    public function getValue(BoxRepository $boxRepository)
    {
        $total = $boxRepository->count();
        $inUse = $boxRepository->countAllInUse();
        $removed = $boxRepository->countAllRemoved();
        $abandoned = $boxRepository->countAllAbandoned();
        $totalSpaces = Meta::get('member_box_limit');

        return [
            [$totalSpaces, ['Total Spaces']],
            [$totalSpaces - $inUse, ['Available Spaces']],
            [$inUse, ['In Use']],
            [$removed, ['Removed']],
            [$abandoned, ['Abandoned']],
            [$total, ['Total Boxes']],
        ];
    }
}
