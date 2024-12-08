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
        Prometheus::addGauge('Tool status')
            ->name('statistics_tool_state')
            ->label('tool')
            ->helpText('Tool status, Disabled: -1, Free: 0, In Use: 1')
            ->value(fn () => app()->call([$this, 'getValueState']));

        Prometheus::addGauge('Tool user counts')
            ->name('statistics_tool_users')
            ->labels([
                'tool',
                'role',
            ])
            ->helpText('Tool Users by role')
            ->value(fn () => app()->call([$this, 'getValueUsers']));
    }

    public function getValueState(ToolRepository $toolRepository)
    {
        $tools = $toolRepository->findAll();
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
    }

    public function getValueUsers(
        ToolRepository $toolRepository,
        GenerateToolStatistics $generateToolStatisticsAction,
    ) {
        $tools = $toolRepository->findAll();
        $toolsStats = $generateToolStatisticsAction->execute();
        $values = [];

        foreach ($tools as $tool) {
            if (! array_key_exists($tool->getDisplayName(), $toolsStats)) {
                continue;
            }

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
    }
}
