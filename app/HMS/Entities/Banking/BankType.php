<?php

namespace HMS\Entities\Banking;

abstract class BankType
{
    /*
     * Fully Automatic bank where new transactions are uploaded via the api endpoint
     */
    public const AUTOMATIC = 'AUTOMATIC';

    /*
     * Transactions are manually entered (via web interface) record of a payment or purchase or via the api.
     */
    public const MANUAL = 'MANUAL';

    /*
     * Special case MANUAL to represent petty cash account.
     */
    public const CASH = 'CASH';

    /**
     * String representation of types for display.
     */
    public const TYPE_STRINGS = [
        self::AUTOMATIC => 'Automatic',
        self::MANUAL => 'Manual',
        self::CASH => 'Cash',
    ];
}
