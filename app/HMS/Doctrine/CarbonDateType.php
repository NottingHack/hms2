<?php

namespace HMS\Doctrine;

class CarbonDateType extends CarbonType
{
    /**
     * {@inheritDoc}
     */
    protected $getFormatString = 'getDateFormatString';
}
