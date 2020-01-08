<?php

namespace HMS\Entities\GateKeeper;

abstract class AccessLogResult
{
    /**
     * Access was denied.
     */
    const ACCESS_DENIED = 10;

    /**
     * Access was granted.
     */
    const ACCESS_GRANTED = 20;

    /**
     * String representation of states for display.
     */
    const RESULT_STRINGS =
    [
        self::ACCESS_DENIED => 'Denied',
        self::ACCESS_GRANTED => 'Granted',
    ];
}
