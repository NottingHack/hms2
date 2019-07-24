<?php

namespace App\Charts;

use ConsoleTVs\Charts\Classes\Chartjs\Chart;

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

        $this->options([
            'scales' => [
                'yAxes' => [
                    [
                        'stacked' => true,
                        'scaleLabel' => [
                            'display' => true,
                            'labelString' => 'kW Hours used',
                        ],
                    ],
                ],
            ],
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

            $rand_color = '#' . substr(str_shuffle('ABCDEF0123456789'), 0, 6);

            $this->dataset($meter->getName(), 'line', $meterReadings->values())
                ->color($rand_color);
        }
    }
}
