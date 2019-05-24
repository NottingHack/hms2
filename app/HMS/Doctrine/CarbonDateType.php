<?php

namespace HMS\Doctrine;

class CarbonDateType extends CarbonType
{
    /**
     * {@inheritdoc}
     */
    protected $getFormatString = 'getDateFormatString';
}
