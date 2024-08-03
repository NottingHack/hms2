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
        $boxRepository = app(BoxRepository::class);

        $total = $boxRepository->count();
        $inUse = $boxRepository->countAllInUse();
        $removed = $boxRepository->countAllRemoved();
        $abandoned = $boxRepository->countAllAbandoned();
        $totalSpaces = Meta::get('member_box_limit');

        Prometheus::addGauge('Member Boxes count')
            ->label('count_of')
            ->helpText('Members Box usages')
            ->value([
                [$totalSpaces, ['Total Spaces']],
                [$totalSpaces - $inUse, ['Available Spaces']],
                [$inUse, ['In Use']],
                [$removed, ['Removed']],
                [$abandoned, ['Abandoned']],
                [$total, ['Total Boxes']],
            ]);
    }
}
