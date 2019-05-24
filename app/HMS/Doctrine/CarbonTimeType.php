<?php

namespace HMS\Doctrine;

class CarbonTimeType extends CarbonType
{
    /**
     * {@inheritdoc}
     */
    protected $getFormatString = 'getTimeFormatString';
}
