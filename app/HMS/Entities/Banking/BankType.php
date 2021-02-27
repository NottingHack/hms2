<?php

namespace HMS\Entities\Banking;

abstract class BankType
{
    /*
     * Fully Automatic bank where new transactions are uploaded via the api endpoint
     */
    const AUTOMATIC = 'AUTOMATIC';

    /*
     * Transactions are manually entered (via web interface) record of a payment or purchase or via the api.
     */
    const MANUAL = 'MANUAL';

    /*
     * Special case MANUAL to represent petty cash account.
     */
    const CASH = 'CASH';

    /**
     * String representation of types for display.
     */
    const TYPE_STRINGS = [
        self::AUTOMATIC => 'Automatic',
        self::MANUAL => 'Manual',
        self::CASH => 'Cash',
    ];
}
