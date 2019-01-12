<<?php

namespace HMS\Entities\Snackspace;

abstract class VendingMachineType
{
    /**
     * A vending machine.
     */
    const VEND = 'VEND';

    /**
     * A cash acceptor.
     */
    const NOTE = 'NOTE';

    /**
     * String representation of machine types for display.
     */
    const TYPE_STRINGS =
    [
        self::VEND => 'Vending Machine',
        self::NOTE => 'Cash Acceptor',
    ];
}
