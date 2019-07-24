<?php

namespace HMS\Repositories\Instrumentation;

use App\Charts\ElectricReadingsChart;
use HMS\Entities\Instrumentation\ElectricMeter;
use HMS\Entities\Instrumentation\ElectricReading;

interface ElectricReadingRepository
{
    /**
     * Finds all entities in the repository.
     *
     * @return ElectricReading[]
     */
    public function findAll();

    /**
     * Finds latest reading for a meter.
     *
     * @param ElectricMeter $meter
     *
     * @return ElectricReading|null
     */
    public function findLatestReadingForMeter(ElectricMeter $meter);

    /**
     * Return a Chart of all readings;
     *
     * @param ElectricMeter[] $meters
     *
     * @return ElectricReadingsChart|null
     */
    public function chartReadingsForMeters($meters);

    /**
     * Save ElectricReading to the DB.
     *
     * @param ElectricReading $electricReading
     */
    public function save(ElectricReading $electricReading);
}
