<?php

namespace HMS\Doctrine;

use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateTimeType;
use InvalidArgumentException;

class CarbonType extends DateTimeType
{
    /**
     * @var null|\DateTimeZone
     */
    private static $utc = null;

    /**
     * Function name to call on AbstractPlatform to get the relevent string format.
     *
     * @var string
     */
    protected $getFormatString = 'getDateTimeFormatString';

    /**
     * Converts the Datetime to UTC before storing to database.
     *
     * @param mixed $value
     * @param AbstractPlatform $platform
     *
     * @return mixed|null
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        if (is_null(self::$utc)) {
            self::$utc = new \DateTimeZone('UTC');
        }

        if ($value instanceof DateTime) {
            $value->setTimezone(self::$utc);
        }

        return parent::convertToDatabaseValue($value, $platform);
    }

    /**
     * Converts the Datetime from UTC to default timezone.
     *
     * @param mixed $value
     * @param AbstractPlatform $platform
     *
     * @return Carbon|null
     * @throws ConversionException
     * @throws InvalidArgumentException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null || $value instanceof Carbon) {
            return $value;
        }

        if (is_null(self::$utc)) {
            self::$utc = new \DateTimeZone('UTC');
        }

        $converted = Carbon::createFromFormat(
            $platform->{$this->getFormatString}(),
            $value,
            self::$utc
        );
        $converted->setTimezone(new DateTimeZone(date_default_timezone_get()));

        if (! $converted) {
            throw ConversionException::conversionFailedFormat(
                $value,
                $this->getName(),
                $platform->{$this->getFormatString}()
            );
        }

        return $converted;
    }
}
