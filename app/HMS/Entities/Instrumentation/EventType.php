<?php

namespace HMS\Entities\Instrumentation;

abstract class EventType
{
    const LAST_OUT = 'LAST_OUT';

    const FIRST_IN = 'FIRST_IN';

    const DOOR_OPENED = 'DOOR_OPENED';

    const DOOR_CLOSED = 'DOOR_CLOSED';

    const DOOR_TIMEOUT = 'DOOR_TIMEOUT';

    const DOORBELL = 'DOORBELL';

    const PROCESS_RESTART = 'PROCESS_RESTART';

    const WARN = 'WARN';

    const DOOR_LOCKED = 'DOOR_LOCKED';

    const UNKNOWN = 'UNKNOWN';

    /**
     * String representation of types for display.
     */
    const TYPE_STRINGS = [
        self::LAST_OUT          => 'Last Out',
        self::FIRST_IN          => 'First In',
        self::DOOR_OPENED       => 'Door Opened',
        self::DOOR_CLOSED       => 'Door Closed',
        self::DOOR_TIMEOUT      => 'Door Timeout',
        self::DOORBELL          => 'Doorbell',
        self::PROCESS_RESTART   => 'Process Restart',
        self::WARN              => 'Warn',
        self::DOOR_LOCKED       => 'Door Locked',
        self::UNKNOWN           => 'Unknown',
    ];
}
