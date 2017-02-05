<?php

namespace HMS\Doctrine;

use Carbon\Carbon;
use Doctrine\DBAL\Types\DateTimeType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class CarbonType extends DateTimeType
{
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $result = parent::convertToPHPValue($value, $platform);

        if ($result instanceof \DateTime) {
            return Carbon::instance($result);
        }

        return $result;
    }
}
