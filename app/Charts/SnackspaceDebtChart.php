<?php

namespace App\Charts;

use ConsoleTVs\Charts\Classes\Highcharts\Chart;

class SnackspaceDebtChart extends Chart
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
        $this->title('Snackspace');
        $this->label('£');

        $this->options([
            'chart' => [
                'zoomType' => 'x',
            ],
            'subtitle' => [
                'text' => 'For all time, click/pinch to zoom.',
            ],
            'tooltip' => [
                'split' => true,
                'valuePrefix' => '£',
            ],
        ]);
    }
}
