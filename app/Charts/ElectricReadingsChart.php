<?php

namespace App\Charts;

use ConsoleTVs\Charts\Classes\Highcharts\Chart;

class ElectricReadingsChart extends Chart
{
    /**
     * Initializes the chart.
     *
     * @param ElectricMeter[] $meters
     * @param array $readings
     *
     * @return void
     */
    public function __construct($meters, $readings)
    {
        parent::__construct();

        $this->height = 600;
        $this->title('Electricity usage');
        $this->label('kW Hours');

        $this->options([
            'chart' => [
                'zoomType' => 'x',
            ],
            'subtitle' => [
                'text' => 'kW hours since previous reading.',
            ],
            'tooltip' => [
                'split' => true,
                'valueSuffix' => ' kW Hours',
            ],
            'plotOptions' => [
                'area' => [
                    'stacking' => 'normal',
                ]
            ]
        ]);

        $readings = collect($readings);

        $readingLabels = $readings->map(function ($reading) {
            return $reading['date']->toDateString();
        });
        $this->labels($readingLabels);

        $previousReading = null;

        foreach ($meters as $meter) {
            $previousReading = null;
            $meterReadings = $readings->mapWithKeys(function ($reading) use ($meter, &$previousReading) {
                if (! is_null($previousReading) && ! is_null($reading[$meter->getName()])) {
                    $unitsUsed = $reading[$meter->getName()] - $previousReading;
                } else {
                    $unitsUsed = null;
                }
                $previousReading = $reading[$meter->getName()];

                return [
                    $reading['date']->toDateString() => $unitsUsed,
                ];
            });

            $this->dataset($meter->getName(), 'area', $meterReadings->values());
        }
    }
}
