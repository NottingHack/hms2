<<?php

namespace HMS\Entities\Snackspace;

abstract class VendingMachineConnection
{
    /**
     * A vending machine.
     */
    const UDP = 'UDP';

    /**
     * A cash acceptor.
     */
    const MQTT = 'MQTT';

    /**
     * String representation of machine connection type for display.
     */
    const CONNECTION_STRINGS =
    [
        self::UDP => 'UDP',
        self::MQTT => 'MQTT',
    ];
}
