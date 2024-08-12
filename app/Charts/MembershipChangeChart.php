<?php

namespace App\Charts;

use ConsoleTVs\Charts\Classes\Highcharts\Chart;
use HMS\Views\MembershipChangeCounts;

class MembershipChangeChart extends Chart
{
    /**
     * Initializes the chart.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->height = 800;
        $this->title('Membership Graph');

        $this->options([
            'chart' => [
                'zoomType' => 'x',
            ],
            'subtitle' => [
                // 'text' => 'Membership change over time.',
            ],
            'tooltip' => [
                'split' => true,
            ],
        ]);

        $data = collect();
        $membershipChangeCounts = MembershipChangeCounts::all()
            ->each(function ($membershipChangeCount) use (&$data) {
                $data[$membershipChangeCount->date] = [
                    'current' => $data->last(null, ['current' => 0])['current']
                        + $membershipChangeCount->current_added
                        + $membershipChangeCount->young_added
                        - $membershipChangeCount->current_removed
                        - $membershipChangeCount->young_removed,
                    'ex' => $data->last(null, ['ex' => 0])['ex']
                        + ($membershipChangeCount->ex_added
                            + $membershipChangeCount->temporarybanned_added
                            + $membershipChangeCount->banned_added)
                        - ($membershipChangeCount->ex_removed
                            + $membershipChangeCount->temporarybanned_removed
                            + $membershipChangeCount->banned_removed),
                ];
            });

        $this->labels($data->keys());

        $this->dataset('Current', 'line', $data->pluck('current'));
        $this->dataset('Ex', 'line', $data->pluck('ex'));
    }
}
