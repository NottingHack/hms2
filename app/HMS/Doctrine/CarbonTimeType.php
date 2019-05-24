<?php

namespace HMS\Doctrine;

class CarbonTimeType extends CarbonType
{
    /**
     * {@inheritDoc}
     */
    protected $getFormatString = 'getTimeFormatString';
}
