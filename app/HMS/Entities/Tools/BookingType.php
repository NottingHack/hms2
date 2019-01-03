<?php

namespace HMS\Entities\Tools;

abstract class BookingType
{
    /**
     * Regular booking.
     */
    const NORMAL = 'NORMAL';

    /**
     * Tool is booked for an Induction.
     */
    const INDUCTION = 'INDUCTION';

    /**
     * Tool is booked for maintenance.
     */
    const MAINTENANCE = 'MAINTENANCE';

    /**
     * String representation of states for display.
     */
    const TYPE_STRINGS =
    [
        self::NORMAL => 'Normal',
        self::INDUCTION => 'Induction',
        self::MAINTENANCE => 'Maintenance',
    ];
}
