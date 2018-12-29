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
    const INDCUTION = 'INDCUTION';

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
        self::INDCUTION => 'Induction',
        self::MAINTENANCE => 'Maintenance',
    ];
}
