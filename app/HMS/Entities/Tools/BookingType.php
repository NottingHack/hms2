<?php

namespace HMS\Entities\Tools;

abstract class BookingType
{
    /**
     * Regular booking.
     */
    public const NORMAL = 'NORMAL';

    /**
     * Tool is booked for an Induction.
     */
    public const INDUCTION = 'INDUCTION';

    /**
     * Tool is booked for maintenance.
     */
    public const MAINTENANCE = 'MAINTENANCE';

    /**
     * String representation of types for display.
     */
    public const TYPE_STRINGS =
    [
        self::NORMAL => 'Normal',
        self::INDUCTION => 'Induction',
        self::MAINTENANCE => 'Maintenance',
    ];
}
