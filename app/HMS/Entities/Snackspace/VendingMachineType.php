<?php

namespace HMS\Entities\Snackspace;

abstract class VendingMachineType
{
    /**
     * A vending machine.
     */
    public const VEND = 'VEND';

    /**
     * A cash acceptor.
     */
    public const NOTE = 'NOTE';

    /**
     * String representation of machine types for display.
     */
    public const TYPE_STRINGS =
    [
        self::VEND => 'Vending Machine',
        self::NOTE => 'Cash Acceptor',
    ];
}
