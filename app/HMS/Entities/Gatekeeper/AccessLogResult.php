<?php

namespace HMS\Entities\Gatekeeper;

abstract class AccessLogResult
{
    /**
     * Access was denied.
     */
    public const ACCESS_DENIED = 10;

    /**
     * Access was granted.
     */
    public const ACCESS_GRANTED = 20;

    /**
     * String representation of states for display.
     */
    public const RESULT_STRINGS = [
        self::ACCESS_DENIED => 'Denied',
        self::ACCESS_GRANTED => 'Granted',
    ];
}
