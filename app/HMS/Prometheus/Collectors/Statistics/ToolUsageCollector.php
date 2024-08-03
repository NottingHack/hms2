<?php

namespace HMS\Prometheus\Collectors\Statistics;

use HMS\Actions\Statistics\GenerateToolStatistics;
use HMS\Entities\Tools\ToolState;
use HMS\Repositories\Tools\ToolRepository;
use Spatie\Prometheus\Collectors\Collector;
use Spatie\Prometheus\Facades\Prometheus;

class ToolUsageCollector implements Collector
{
    public function register(): void
    {
        $toolRepository = app(ToolRepository::class);
        $tools = $toolRepository->findAll();
        $generateToolStatisticsAction = app(GenerateToolStatistics::class);

        $toolsStats = $generateToolStatisticsAction->execute();

        Prometheus::addGauge('Tool status')
            ->name('statistics_tool_state')
            ->label('tool')
            ->helpText('Tool status, Disabled: -1, Free: 0, In Use: 1')
            ->value(function () use ($tools) {
                $values = [];
                foreach ($tools as $tool) {
                    $values[] = [
                        match ($tool->getStatus()) {
                            ToolState::DISABLED => -1,
                            ToolState::FREE => 0,
                            ToolState::IN_USE => 1,
                        },
                        [$tool->getName()],
                    ];
                }

                return $values;
            });

        Prometheus::addGauge('Tool user counts')
            ->name('statistics_tool_users')
            ->labels([
                'tool',
                'role',
            ])
            ->helpText('Tool status, Disabled: -1, Free: 0, In Use: 1')
            ->value(function () use ($tools, $toolsStats) {
                $values = [];
                foreach ($tools as $tool) {
                    $values[] = [
                        $toolsStats[$tool->getDisplayName()]['userCount'],
                        [$tool->getName(), 'user'],
                    ];
                    $values[] = [
                        $toolsStats[$tool->getDisplayName()]['inductorCount'],
                        [$tool->getName(), 'inductor'],
                    ];
                    $values[] = [
                        $toolsStats[$tool->getDisplayName()]['maintainerCount'],
                        [$tool->getName(), 'maintainer'],
                    ];
                }

                return $values;
            });
    }
}
