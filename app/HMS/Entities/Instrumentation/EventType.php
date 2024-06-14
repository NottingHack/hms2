<?php

namespace HMS\Entities\Instrumentation;

abstract class EventType
{
    public const LAST_OUT = 'LAST_OUT';
    public const FIRST_IN = 'FIRST_IN';
    public const DOOR_OPENED = 'DOOR_OPENED';
    public const DOOR_CLOSED = 'DOOR_CLOSED';
    public const DOOR_TIMEOUT = 'DOOR_TIMEOUT';
    public const DOORBELL = 'DOORBELL';
    public const PROCESS_RESTART = 'PROCESS_RESTART';
    public const WARN = 'WARN';
    public const DOOR_LOCKED = 'DOOR_LOCKED';
    public const UNKNOWN = 'UNKNOWN';

    /**
     * String representation of types for display.
     */
    public const TYPE_STRINGS = [
        self::LAST_OUT => 'Last Out',
        self::FIRST_IN => 'First In',
        self::DOOR_OPENED => 'Door Opened',
        self::DOOR_CLOSED => 'Door Closed',
        self::DOOR_TIMEOUT => 'Door Timeout',
        self::DOORBELL => 'Doorbell',
        self::PROCESS_RESTART => 'Process Restart',
        self::WARN => 'Warn',
        self::DOOR_LOCKED => 'Door Locked',
        self::UNKNOWN => 'Unknown',
    ];
}
