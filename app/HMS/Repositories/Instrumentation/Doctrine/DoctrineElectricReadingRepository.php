<?php

namespace HMS\Repositories\Instrumentation\Doctrine;

use App\Charts\ElectricReadingsChart;
use Doctrine\ORM\EntityRepository;
use HMS\Entities\Instrumentation\ElectricMeter;
use HMS\Entities\Instrumentation\ElectricReading;
use HMS\Repositories\Instrumentation\ElectricReadingRepository;

class DoctrineElectricReadingRepository extends EntityRepository implements ElectricReadingRepository
{
    /**
     * Finds all entities in the repository.
     *
     * @return ElectricReading[]
     */
    public function findAll()
    {
        return parent::findAll();
    }

    /**
     * Finds latest reading for a meter.
     *
     * @param ElectricMeter $meter
     *
     * @return ElectricReading|null
     */
    public function findLatestReadingForMeter(ElectricMeter $meter)
    {
        return parent::findOneBy(['meter' => $meter], ['date' => 'DESC']);
    }

    /**
     * Return a Chart of all readings;.
     *
     * @param ElectricMeter[] $meters
     *
     * @return ElectricReadingsChart|null
     */
    public function chartReadingsForMeters($meters)
    {
        $query = parent::createQueryBuilder('er')
                    ->select('er.date');

        $subqueries = [];
        foreach ($meters as $meter) {
            $subquery = parent::createQueryBuilder('er' . $meter->getId())
                ->select('er' . $meter->getId() . '.reading')
                ->where('er' . $meter->getId() . '.date = er.date')
                ->andWhere('er' . $meter->getId() . '.meter = ' . $meter->getId())
                ->getDQL();

            $query->addSelect('(' . $subquery . ') AS ' . $meter->getName());
        }

        $query->groupBy('er.date')
            ->orderBy('er.date', 'ASC');

        $results = $query->getQuery()->getResult();
        // TODO: chcek results is not null

        $readingChart = new ElectricReadingsChart($meters, $results);

        return $readingChart;
    }

    /**
     * Save ElectricReading to the DB.
     *
     * @param ElectricReading $electricReading
     */
    public function save(ElectricReading $electricReading)
    {
        $this->_em->persist($electricReading);
        $this->_em->flush();
    }
}
