<?php

namespace HMS\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \HMS\Helpers\Features
 */
class Features extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Features';
    }
}
