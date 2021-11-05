<?php

namespace HMS\Entities\Snackspace;

abstract class VendingMachineConnectionType
{
    /**
     * A vending machine.
     */
    public const UDP = 'UDP';

    /**
     * A cash acceptor.
     */
    public const MQTT = 'MQTT';

    /**
     * String representation of machine connection type for display.
     */
    public const CONNECTION_STRINGS =
    [
        self::UDP => 'UDP',
        self::MQTT => 'MQTT',
    ];
}
