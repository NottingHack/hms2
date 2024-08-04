<?php

namespace HMS\Entities\Gatekeeper;

abstract class DoorState
{
    /**
     * Door state has never been reported
     */
    public const UNKNOWN = 'UNKNOWN';
    /**
     * Door is open
     */
    public const OPEN = 'OPEN';
    /**
     * Door is closed and not locked
     */
    public const CLOSED = 'CLOSED';
    /**
     * Door is closed and (should be) locked
     */
    public const LOCKED = 'LOCKED';
    /**
     * Shouldn't be possible to get here...
     */
    public const FAULT = 'FAULT';

    /**
     * String representation of states for display.
     */
    public const STATE_STRINGS =
    [
        self::UNKNOWN => 'Unknown',
        self::OPEN => 'Open',
        self::CLOSED => 'Closed',
        self::LOCKED => 'Locked',
        self::FAULT => 'Fault',
    ];
}
